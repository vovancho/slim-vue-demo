<?php

declare(strict_types=1);

namespace Api\Test\Unit\Model\Task\Entity\Task;


use Api\Model\Task\Entity\Task\Event\TaskCanceled;
use Api\Model\Task\Entity\Task\Task;
use Api\Test\Builder\Task\TaskBuilder;
use PHPUnit\Framework\TestCase;

class CancelTest extends TestCase
{
    public function testSuccessAsWait(): void
    {
        $task = $this->create();
        $task->cancel();

        self::assertFalse($task->isWait());
        self::assertFalse($task->isExecute());
        self::assertFalse($task->isExecuting());
        self::assertFalse($task->isComplete());
        self::assertTrue($task->isInterrupted());
        self::assertTrue($task->isCancel());
        self::assertFalse($task->isError());

        self::assertEquals(0, $task->getProcessPercent());
        self::assertNull($task->getErrorMessage());

        $canceledEvents = array_filter($task->releaseEvents(), fn ($class) => get_class($class) === TaskCanceled::class);
        self::assertNotEmpty($canceledEvents);
        $canceledEvent = array_shift($canceledEvents);
        self::assertEquals($task->getId(), $canceledEvent->id);
        self::assertEquals($task->getType(), $canceledEvent->type);
        self::assertEquals($task->getUser(), $canceledEvent->user);
    }

    public function testSuccessAsProcessed(): void
    {
        $task = $this->createAsExecuted();
        $task->addPercent($percent = 50);
        $task->cancel();

        self::assertFalse($task->isWait());
        self::assertFalse($task->isExecute());
        self::assertFalse($task->isExecuting());
        self::assertFalse($task->isComplete());
        self::assertTrue($task->isInterrupted());
        self::assertTrue($task->isCancel());
        self::assertFalse($task->isError());

        self::assertEquals($percent, $task->getProcessPercent());
        self::assertNull($task->getErrorMessage());

        $canceledEvents = array_filter($task->releaseEvents(), fn ($class) => get_class($class) === TaskCanceled::class);
        self::assertNotEmpty($canceledEvents);
        $canceledEvent = array_shift($canceledEvents);
        self::assertEquals($task->getId(), $canceledEvent->id);
        self::assertEquals($task->getType(), $canceledEvent->type);
        self::assertEquals($task->getUser(), $canceledEvent->user);
    }

    public function testComplete(): void
    {
        $task = $this->createAsComplete();
        $this->expectExceptionMessage('Задание не может быть отменено.');
        $task->cancel();
    }

    public function testAlreadyCancel(): void
    {
        $task = $this->create();
        $task->cancel();
        $this->expectExceptionMessage('Задание не может быть отменено.');
        $task->cancel();
    }

    public function testError(): void
    {
        $task = $this->createAsError();
        $this->expectExceptionMessage('Задание не может быть отменено.');
        $task->cancel();
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

    private function createAsComplete(): Task
    {
        $task = $this->createAsExecuted();
        $task->addPercent(100);
        $task->complete();
        return $task;
    }

    private function createAsError(): Task
    {
        $task = $this->createAsExecuted();
        $task->error(new \Exception());
        return $task;
    }
}
