<?php

declare(strict_types=1);

namespace App\TaskHandler\Test\Unit\Entity\Task\Task;

use App\TaskHandler\Entity\Task\Event\TaskCompleted;
use App\TaskHandler\Test\Builder\TaskBuilder;
use PHPUnit\Framework\TestCase;

class CompleteTest extends TestCase
{
    public function testSuccess(): void
    {
        $task = (new TaskBuilder())->process()->build();
        $task->complete();

        self::assertFalse($task->isWait());
        self::assertFalse($task->isExecute());
        self::assertTrue($task->isComplete());
        self::assertFalse($task->isInterrupted());
        self::assertFalse($task->isCancel());
        self::assertFalse($task->isError());

        self::assertEquals(100, $task->getProcessPercent());
        self::assertNull($task->getError());

        $completedEvents = array_filter(
            $task->releaseEvents(),
            fn ($class) => get_class($class) === TaskCompleted::class
        );
        self::assertNotEmpty($completedEvents);
        $completedEvent = array_shift($completedEvents);
        self::assertEquals($task->getVisibility(), $completedEvent->visibility);
        self::assertEquals($task->getAuthor(), $completedEvent->author);
    }

    public function testNotExecute(): void
    {
        $task = (new TaskBuilder())->build();
        $this->expectExceptionMessage('Задание может быть выполнено только в статусе выполнения.');
        $task->complete();
    }

    public function testNot100PercentProcessed(): void
    {
        $task = (new TaskBuilder())->execute()->build();
        $task->addPercent(99);
        $this->expectExceptionMessage('Задание не может быть выполнено с процентом выполнения меньше 100.');
        $task->complete();
    }
}
