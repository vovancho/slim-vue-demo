<?php

declare(strict_types=1);

namespace App\TaskHandler\Test\Unit\Entity\Task\Task;

use App\TaskHandler\Entity\Task\Event\TaskError;
use App\TaskHandler\Test\Builder\TaskBuilder;
use Exception;
use PHPUnit\Framework\TestCase;

class ErrorTest extends TestCase
{
    public function testSuccess(): void
    {
        $task = (new TaskBuilder())->execute()->build();
        $task->addPercent($percent = 15);
        $task->error($exception = new Exception('error'));

        self::assertFalse($task->isWait());
        self::assertFalse($task->isExecute());
        self::assertFalse($task->isComplete());
        self::assertTrue($task->isInterrupted());
        self::assertFalse($task->isCancel());
        self::assertTrue($task->isError());

        self::assertEquals($percent, $task->getProcessPercent());
        $error = $task->getError();

        self::assertEquals($exception->getMessage(), $error->getMessage());
        self::assertNotEmpty($exception->getTraceAsString(), $error->getTrace());

        $processedEvents = array_filter($task->releaseEvents(), fn ($class) => get_class($class) === TaskError::class);
        self::assertNotEmpty($processedEvents);
        $processedEvent = array_shift($processedEvents);
        self::assertEquals($task->getVisibility(), $processedEvent->visibility);
        self::assertEquals($task->getAuthor(), $processedEvent->author);
    }

    public function testNotExecute(): void
    {
        $task = (new TaskBuilder())->build();
        $this->expectExceptionMessage('Ошибка выполнения возможна только при выполнении задачи.');
        $task->error(new Exception('error'));
    }
}
