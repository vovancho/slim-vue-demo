<?php

declare(strict_types=1);

namespace App\TaskHandler\Test\Unit\Entity\Task;

use App\TaskHandler\Entity\Task\Visibility;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\TaskHandler\Entity\Task\Visibility
 */
class VisibilityTest extends TestCase
{
    public function testSuccess(): void
    {
        $status = new Visibility($name = Visibility::PUBLIC);

        self::assertEquals($name, $status->getName());
    }

    public function testIncorrect(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Visibility('none');
    }

    public function testPublic(): void
    {
        $visibility = Visibility::public();

        self::assertTrue($visibility->isPublic());
        self::assertFalse($visibility->isPrivate());
    }

    public function testPrivate(): void
    {
        $visibility = Visibility::private();

        self::assertFalse($visibility->isPublic());
        self::assertTrue($visibility->isPrivate());
    }
}
