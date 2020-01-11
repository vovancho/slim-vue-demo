<?php

declare(strict_types=1);

namespace Api\Test\Unit\Model\Task\Entity\Task;

use Api\Model\Task\Entity\Task\Event\TaskExecuted;
use Api\Model\Task\Entity\Task\Task;
use Api\Test\Builder\Task\TaskBuilder;
use PHPUnit\Framework\TestCase;

class ExecuteTest extends TestCase
{
    public function testSuccess(): void
    {
        $task = $this->create();
        $task->execute();

        self::assertFalse($task->isWait());
        self::assertTrue($task->isExecute());
        self::assertTrue($task->isExecuting());
        self::assertFalse($task->isComplete());
        self::assertFalse($task->isInterrupted());
        self::assertFalse($task->isCancel());
        self::assertFalse($task->isError());

        $executedEvents = array_filter($task->releaseEvents(), fn ($class) => get_class($class) === TaskExecuted::class);
        self::assertNotEmpty($executedEvents);
        $executedEvent = array_shift($executedEvents);
        self::assertEquals($task->getId(), $executedEvent->id);
        self::assertEquals($task->getUser(), $executedEvent->user);
    }

    public function testAlreadyExecute(): void
    {
        $task = $this->createAsExecuted();
        $this->expectExceptionMessage('Выполнение задачи возможно только в статусе ожидания.');
        $task->execute();
    }

    private function create(): Task
    {
        return (new TaskBuilder)->build();
    }

    private function createAsExecuted(): Task
    {
        $task = (new TaskBuilder)->build();
        $task->execute();
        return $task;
    }
}
