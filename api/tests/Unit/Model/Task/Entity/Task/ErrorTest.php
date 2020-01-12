<?php

declare(strict_types=1);

namespace Api\Test\Unit\Model\Task\Entity\Task;


use Api\Model\Task\Entity\Task\Event\TaskError;
use Api\Model\Task\Entity\Task\Task;
use Api\Test\Builder\Task\TaskBuilder;
use PHPUnit\Framework\TestCase;

class ErrorTest extends TestCase
{
    public function testSuccess(): void
    {
        $task = $this->createAsExecuted();
        $task->addPercent($percent = 15);
        $task->error($exception = new \Exception('error'));

        self::assertFalse($task->isWait());
        self::assertFalse($task->isExecute());
        self::assertFalse($task->isExecuting());
        self::assertFalse($task->isComplete());
        self::assertTrue($task->isInterrupted());
        self::assertFalse($task->isCancel());
        self::assertTrue($task->isError());

        self::assertEquals($percent, $task->getProcessPercent());
        $errorMessage = json_decode($task->getErrorMessage(), true);

        self::assertArrayHasKey('message', $errorMessage);
        self::assertArrayHasKey('trace', $errorMessage);
        self::assertEquals($exception->getMessage(), $errorMessage['message']);

        $processedEvents = array_filter($task->releaseEvents(), fn ($class) => get_class($class) === TaskError::class);
        self::assertNotEmpty($processedEvents);
        $processedEvent = array_shift($processedEvents);
        self::assertEquals($task->getId(), $processedEvent->task->getId());
        self::assertEquals($task->getType(), $processedEvent->type);
        self::assertEquals($task->getUser(), $processedEvent->user);
    }

    public function testNotExecute(): void
    {
        $task = $this->create();
        $this->expectExceptionMessage('Ошибка выполнения возможна только при выполнении задачи.');
        $task->error(new \Exception('error'));
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
