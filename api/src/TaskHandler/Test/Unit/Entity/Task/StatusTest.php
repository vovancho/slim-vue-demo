<?php

declare(strict_types=1);

namespace App\TaskHandler\Test\Unit\Entity\Task;

use App\TaskHandler\Entity\Task\Status;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\TaskHandler\Entity\Task\Status
 */
class StatusTest extends TestCase
{
    public function testSuccess(): void
    {
        $status = new Status($name = Status::WAIT);

        self::assertEquals($name, $status->getName());
    }

    public function testIncorrect(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Status('none');
    }

    public function testWait(): void
    {
        $status = Status::wait();

        self::assertTrue($status->isWait());
        self::assertFalse($status->isExecute());
        self::assertFalse($status->isComplete());
        self::assertFalse($status->isInterrupted());
        self::assertFalse($status->isCancel());
        self::assertFalse($status->isError());
    }

    public function testExecute(): void
    {
        $status = Status::execute();

        self::assertFalse($status->isWait());
        self::assertTrue($status->isExecute());
        self::assertFalse($status->isComplete());
        self::assertFalse($status->isInterrupted());
        self::assertFalse($status->isCancel());
        self::assertFalse($status->isError());
    }

    public function testComplete(): void
    {
        $status = Status::complete();

        self::assertFalse($status->isWait());
        self::assertFalse($status->isExecute());
        self::assertTrue($status->isComplete());
        self::assertFalse($status->isInterrupted());
        self::assertFalse($status->isCancel());
        self::assertFalse($status->isError());
    }

    public function testCancel(): void
    {
        $status = Status::cancel();

        self::assertFalse($status->isWait());
        self::assertFalse($status->isExecute());
        self::assertFalse($status->isComplete());
        self::assertTrue($status->isInterrupted());
        self::assertTrue($status->isCancel());
        self::assertFalse($status->isError());
    }

    public function testError(): void
    {
        $status = Status::error();

        self::assertFalse($status->isWait());
        self::assertFalse($status->isExecute());
        self::assertFalse($status->isComplete());
        self::assertTrue($status->isInterrupted());
        self::assertFalse($status->isCancel());
        self::assertTrue($status->isError());
    }
}
