<?php

declare(strict_types=1);

namespace App\TaskHandler\Test\Unit\Entity\Task\Task;

use App\TaskHandler\Entity\Task\Event\TaskProcessed;
use App\TaskHandler\Test\Builder\TaskBuilder;
use PHPUnit\Framework\TestCase;

class ProcessedTest extends TestCase
{
    public function testSuccess(): void
    {
        $task = (new TaskBuilder())->execute()->build();
        $task->addPercent($percent = 15);

        self::assertFalse($task->isWait());
        self::assertTrue($task->isExecute());
        self::assertFalse($task->isComplete());
        self::assertFalse($task->isInterrupted());
        self::assertFalse($task->isCancel());
        self::assertFalse($task->isError());

        self::assertEquals($percent, $task->getProcessPercent());
        self::assertNull($task->getError());

        $processedEvents = array_filter(
            $task->releaseEvents(),
            fn ($class) => get_class($class) === TaskProcessed::class
        );
        self::assertNotEmpty($processedEvents);
        $processedEvent = array_shift($processedEvents);
        self::assertEquals($task->getId(), $processedEvent->task->getId());
        self::assertEquals($task->getVisibility(), $processedEvent->visibility);
        self::assertEquals($task->getAuthor(), $processedEvent->author);
    }

    public function testNotExecute(): void
    {
        $task = (new TaskBuilder())->build();
        $this->expectExceptionMessage('Изменение хода выполнения возможно только при выполнении задачи.');
        $task->addPercent(15);
    }

    public function testAddNegativePercent(): void
    {
        $task = (new TaskBuilder())->execute()->build();
        $task->addPercent($percent = -15);
        self::assertEquals(0, $task->getProcessPercent());
    }

    public function testAddMore100Percent(): void
    {
        $task = (new TaskBuilder())->execute()->build();
        $task->addPercent($percent = 115);
        self::assertEquals(100, $task->getProcessPercent());
    }

    public function testIf100Percent(): void
    {
        $task = (new TaskBuilder())->execute()->build();
        $task->addPercent($percent = 100);
        self::assertEquals($percent, $task->getProcessPercent());

        $processedEvents = array_filter(
            $task->releaseEvents(),
            fn ($class) => get_class($class) === TaskProcessed::class
        );
        self::assertEmpty($processedEvents);
    }
}
