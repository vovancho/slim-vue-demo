<?php

declare(strict_types=1);

namespace App\TaskHandler\Entity\Task;

use Webmozart\Assert\Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Error
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private string $message;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private string $trace;

    public function __construct(string $message, string $trace)
    {
        Assert::notEmpty($message);
        Assert::notEmpty($trace);
        $this->message = $message;
        $this->trace = $trace;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getTrace(): string
    {
        return $this->trace;
    }

    public function isEmpty(): bool
    {
        return empty($this->message) && empty($this->trace);
    }
}
