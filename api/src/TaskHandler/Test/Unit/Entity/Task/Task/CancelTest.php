<?php

declare(strict_types=1);

namespace App\TaskHandler\Test\Unit\Entity\Task\Task;

use App\TaskHandler\Entity\Task\Event\TaskCanceled;
use App\TaskHandler\Test\Builder\TaskBuilder;
use PHPUnit\Framework\TestCase;

class CancelTest extends TestCase
{
    public function testSuccessAsWait(): void
    {
        $task = (new TaskBuilder())->build();
        $task->cancel();

        self::assertFalse($task->isWait());
        self::assertFalse($task->isExecute());
        self::assertFalse($task->isComplete());
        self::assertTrue($task->isInterrupted());
        self::assertTrue($task->isCancel());
        self::assertFalse($task->isError());

        self::assertEquals(0, $task->getProcessPercent());
        self::assertNull($task->getError());

        $canceledEvents = array_filter(
            $task->releaseEvents(),
            fn ($class) => get_class($class) === TaskCanceled::class
        );
        self::assertNotEmpty($canceledEvents);
        $canceledEvent = array_shift($canceledEvents);
        self::assertEquals($task->getVisibility(), $canceledEvent->visibility);
        self::assertEquals($task->getAuthor(), $canceledEvent->author);
    }

    public function testSuccessAsProcessed(): void
    {
        $task = (new TaskBuilder())->execute()->build();
        $task->addPercent($percent = 50);
        $task->cancel();

        self::assertFalse($task->isWait());
        self::assertFalse($task->isExecute());
        self::assertFalse($task->isComplete());
        self::assertTrue($task->isInterrupted());
        self::assertTrue($task->isCancel());
        self::assertFalse($task->isError());

        self::assertEquals($percent, $task->getProcessPercent());
        self::assertNull($task->getError());

        $canceledEvents = array_filter(
            $task->releaseEvents(),
            fn ($class) => get_class($class) === TaskCanceled::class
        );
        self::assertNotEmpty($canceledEvents);
        $canceledEvent = array_shift($canceledEvents);
        self::assertEquals($task->getVisibility(), $canceledEvent->visibility);
        self::assertEquals($task->getAuthor(), $canceledEvent->author);
    }

    public function testComplete(): void
    {
        $task = (new TaskBuilder())->complete()->build();
        $this->expectExceptionMessage('Задание не может быть отменено.');
        $task->cancel();
    }

    public function testAlreadyCancel(): void
    {
        $task = (new TaskBuilder())->build();
        $task->cancel();
        $this->expectExceptionMessage('Задание не может быть отменено.');
        $task->cancel();
    }

    public function testError(): void
    {
        $task = (new TaskBuilder())->error()->build();
        $this->expectExceptionMessage('Задание не может быть отменено.');
        $task->cancel();
    }
}
