<?php

declare(strict_types=1);

namespace Api\Test\Unit\Model\Task\Entity\Task;


use Api\Model\Task\Entity\Task\Event\TaskCompleted;
use Api\Model\Task\Entity\Task\Task;
use Api\Test\Builder\Task\TaskBuilder;
use PHPUnit\Framework\TestCase;

class CompleteTest extends TestCase
{
    public function testSuccess(): void
    {
        $task = $this->createAsProcessed();
        $task->complete();

        self::assertFalse($task->isWait());
        self::assertFalse($task->isExecute());
        self::assertFalse($task->isExecuting());
        self::assertTrue($task->isComplete());
        self::assertFalse($task->isInterrupted());
        self::assertFalse($task->isCancel());
        self::assertFalse($task->isError());

        self::assertEquals(100, $task->getProcessPercent());
        self::assertNull($task->getErrorMessage());

        $completedEvents = array_filter($task->releaseEvents(), fn ($class) => get_class($class) === TaskCompleted::class);
        self::assertNotEmpty($completedEvents);
        $completedEvent = array_shift($completedEvents);
        self::assertEquals($task->getId(), $completedEvent->task->getId());
        self::assertEquals($task->getUser(), $completedEvent->user);
    }

    public function testNotExecute(): void
    {
        $task = $this->create();
        $this->expectExceptionMessage('Задание может быть выполнено только в статусе выполнения.');
        $task->complete();
    }

    public function testNot100PercentProcessed(): void
    {
        $task = $this->createAsExecuted();
        $task->addPercent(99);
        $this->expectExceptionMessage('Задание не может быть выполнено с процентом выполнения меньше 100.');
        $task->complete();
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

    private function createAsProcessed(): Task
    {
        $task = $this->createAsExecuted();
        $task->addPercent(100);
        return $task;
    }
}