<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use App\Auth\Entity\User\Event\UserConfirmed;
use App\Auth\Entity\User\Event\UserCreated;
use App\Framework\AggregateRoot;
use App\Framework\EventTrait;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use DomainException;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="auth_users")
 */
class User implements AggregateRoot
{
    use EventTrait;

    /**
     * @ORM\Column(type="auth_user_id")
     * @ORM\Id
     */
    private Id $id;
    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $date;
    /**
     * @ORM\Column(type="auth_user_email", unique=true)
     */
    private Email $email;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $passwordHash;
    /**
     * @ORM\Embedded(class="Token")
     */
    private ?Token $confirmToken = null;
    /**
     * @ORM\Column(type="auth_user_status", length=16)
     */
    private Status $status;

    private function __construct(Id $id, DateTimeImmutable $date, Email $email, Status $status)
    {
        $this->id = $id;
        $this->date = $date;
        $this->email = $email;
        $this->status = $status;
    }

    public static function requestForConfirm(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        string $passwordHash,
        Token $token
    ): self {
        $user = new self($id, $date, $email, Status::wait());
        $user->passwordHash = $passwordHash;
        $user->confirmToken = $token;
        $user->recordEvent(new UserCreated($user->id, $user->email, $user->confirmToken));
        return $user;
    }

    public function confirmSignUp(string $token, DateTimeImmutable $date): void
    {
        if ($this->confirmToken === null) {
            throw new DomainException('Токен подтверждения обязателен.');
        }
        $this->confirmToken->validate($token, $date);
        $this->status = Status::active();
        $this->confirmToken = null;
        $this->recordEvent(new UserConfirmed($this->id));
    }

    public function isWait(): bool
    {
        return $this->status->isWait();
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function getConfirmToken(): ?Token
    {
        return $this->confirmToken;
    }

    /**
     * @ORM\PostLoad()
     */
    public function checkEmbeds(): void
    {
        if ($this->confirmToken && $this->confirmToken->isEmpty()) {
            $this->confirmToken = null;
        }
    }
}
