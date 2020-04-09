<?php

declare(strict_types=1);

namespace App\TaskHandler\Entity\Task;

use Webmozart\Assert\Assert;

class Visibility
{
    public const PRIVATE = 'private';
    public const PUBLIC = 'public';

    private string $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::PRIVATE,
            self::PUBLIC,
        ]);
        $this->name = $name;
    }

    public static function public()
    {
        return new self(self::PUBLIC);
    }

    public static function private()
    {
        return new self(self::PRIVATE);
    }

    public function isPublic()
    {
        return $this->name === self::PUBLIC;
    }

    public function isPrivate()
    {
        return $this->name === self::PRIVATE;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getItems(): array
    {
        return [
            self::PRIVATE,
            self::PUBLIC,
        ];
    }
}
