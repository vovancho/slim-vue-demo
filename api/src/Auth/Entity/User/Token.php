<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Token
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private string $value;
    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private DateTimeImmutable $expires;

    public function __construct(string $value, DateTimeImmutable $expires)
    {
        Assert::notEmpty($value);
        Assert::minLength($value, 6);
        Assert::maxLength($value, 6);
        Assert::digits($value);
        $this->value = $value;
        $this->expires = $expires;
    }

    public function validate(string $token, DateTimeImmutable $date): void
    {
        if (!$this->isEqualTo($token)) {
            throw new DomainException('Неверный код подтверждения.');
        }
        if ($this->isExpiredTo($date)) {
            throw new DomainException('Код подтверждения истек.');
        }
    }

    public function isExpiredTo(DateTimeImmutable $date): bool
    {
        return $this->expires <= $date;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getExpires(): DateTimeImmutable
    {
        return $this->expires;
    }

    public function isEmpty(): bool
    {
        return empty($this->value);
    }

    private function isEqualTo(string $value): bool
    {
        return $this->value === $value;
    }
}
