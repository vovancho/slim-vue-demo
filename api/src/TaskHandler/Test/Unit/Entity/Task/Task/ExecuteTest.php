<?php

declare(strict_types=1);

namespace App\TaskHandler\Test\Unit\Entity\Task\Task;

use App\TaskHandler\Entity\Task\Event\TaskExecuted;
use App\TaskHandler\Test\Builder\TaskBuilder;
use PHPUnit\Framework\TestCase;

class ExecuteTest extends TestCase
{
    public function testSuccess(): void
    {
        $task = (new TaskBuilder())->build();
        $task->execute();

        self::assertFalse($task->isWait());
        self::assertTrue($task->isExecute());
        self::assertFalse($task->isComplete());
        self::assertFalse($task->isInterrupted());
        self::assertFalse($task->isCancel());
        self::assertFalse($task->isError());

        $executedEvents = array_filter(
            $task->releaseEvents(),
            fn ($class) => get_class($class) === TaskExecuted::class
        );
        self::assertNotEmpty($executedEvents);
        $executedEvent = array_shift($executedEvents);
        self::assertEquals($task->getVisibility(), $executedEvent->visibility);
        self::assertEquals($task->getAuthor(), $executedEvent->author);
    }

    public function testAlreadyExecute(): void
    {
        $task = (new TaskBuilder())->execute()->build();

        $this->expectExceptionMessage('Выполнение задачи возможно только в статусе ожидания.');
        $task->execute();
    }
}
