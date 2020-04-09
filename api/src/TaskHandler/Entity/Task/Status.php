<?php

declare(strict_types=1);

namespace App\TaskHandler\Entity\Task;

use Webmozart\Assert\Assert;

class Status
{
    public const WAIT = 'wait';
    public const EXECUTE = 'execute';
    public const COMPLETE = 'complete';
    public const CANCEL = 'cancel';
    public const ERROR = 'error';

    private string $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::WAIT,
            self::EXECUTE,
            self::COMPLETE,
            self::CANCEL,
            self::ERROR,
        ]);
        $this->name = $name;
    }

    public static function wait(): self
    {
        return new self(self::WAIT);
    }

    public static function execute()
    {
        return new self(self::EXECUTE);
    }

    public static function complete()
    {
        return new self(self::COMPLETE);
    }

    public static function cancel()
    {
        return new self(self::CANCEL);
    }

    public static function error()
    {
        return new self(self::ERROR);
    }

    public function isWait()
    {
        return $this->name === self::WAIT;
    }

    public function isExecute()
    {
        return $this->name === self::EXECUTE;
    }

    public function isComplete()
    {
        return $this->name === self::COMPLETE;
    }

    public function isInterrupted()
    {
        return in_array($this->name, [self::CANCEL, self::ERROR]);
    }

    public function isCancel()
    {
        return $this->name === self::CANCEL;
    }

    public function isError()
    {
        return $this->name === self::ERROR;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
