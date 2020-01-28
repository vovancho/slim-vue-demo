<?php

declare(strict_types=1);

namespace Api\Test\Unit\Model\Task\Entity\Task;


use Api\Model\Task\Entity\Task\Event\TaskProcessed;
use Api\Model\Task\Entity\Task\Task;
use Api\Test\Builder\Task\TaskBuilder;
use PHPUnit\Framework\TestCase;

class ProcessedTest extends TestCase
{
    public function testSuccess(): void
    {
        $task = $this->createAsExecuted();
        $task->addPercent($percent = 15);

        self::assertFalse($task->isWait());
        self::assertTrue($task->isExecute());
        self::assertFalse($task->isComplete());
        self::assertFalse($task->isInterrupted());
        self::assertFalse($task->isCancel());
        self::assertFalse($task->isError());

        self::assertEquals($percent, $task->getProcessPercent());
        self::assertNull($task->getErrorMessage());

        $processedEvents = array_filter($task->releaseEvents(), fn ($class) => get_class($class) === TaskProcessed::class);
        self::assertNotEmpty($processedEvents);
        $processedEvent = array_shift($processedEvents);
        self::assertEquals($task->getId(), $processedEvent->task->getId());
        self::assertEquals($task->getType(), $processedEvent->type);
        self::assertEquals($task->getUser(), $processedEvent->user);
    }

    public function testNotExecute(): void
    {
        $task = $this->create();
        $this->expectExceptionMessage('Изменение хода выполнения возможно только при выполнении задачи.');
        $task->addPercent(15);
    }

    public function testAddNegativePercent(): void
    {
        $task = $this->createAsExecuted();
        $task->addPercent($percent = -15);
        self::assertEquals(0, $task->getProcessPercent());
    }

    public function testAddMore100Percent(): void
    {
        $task = $this->createAsExecuted();
        $task->addPercent($percent = 115);
        self::assertEquals(100, $task->getProcessPercent());
    }

    public function testIf100Percent(): void
    {
        $task = $this->createAsExecuted();
        $task->addPercent($percent = 100);
        self::assertEquals($percent, $task->getProcessPercent());

        $processedEvents = array_filter($task->releaseEvents(), fn ($class) => get_class($class) === TaskProcessed::class);
        self::assertEmpty($processedEvents);
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
