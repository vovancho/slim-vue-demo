<?php

declare(strict_types=1);

namespace Api\Test\Unit\Model\Task\Entity\Task;

use Api\Model\Base\Uuid1;
use Api\Model\Task\Entity\Task\Event\TaskCreated;
use Api\Model\Task\Entity\Task\Task;
use Api\Model\User\Entity\User\User;
use Api\Test\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class CreateTest extends TestCase
{
    public function testSuccess(): void
    {
        $task = new Task(
            $id = Uuid1::next(),
            $pushedAt = new \DateTimeImmutable(),
            $user = $this->owner(),
            $type = Task::TYPE_PUBLIC,
            $name = 'Task1'
        );

        self::assertTrue($task->isWait());
        self::assertFalse($task->isExecute());
        self::assertTrue($task->isExecuting());
        self::assertFalse($task->isComplete());
        self::assertFalse($task->isInterrupted());
        self::assertFalse($task->isCancel());
        self::assertFalse($task->isError());

        self::assertEquals($id, $task->getId());
        self::assertEquals($pushedAt, $task->getPushedAt());
        self::assertEquals($user->getId(), $task->getUser()->getId());
        self::assertEquals($type, $task->getType());
        self::assertEquals($name, $task->getName());
        self::assertEquals(0, $task->getProcessPercent());
        self::assertEquals(0, $task->getPosition());
        self::assertNull($task->getErrorMessage());

        $createdEvents = array_filter($task->releaseEvents(), fn ($class) => get_class($class) === TaskCreated::class);
        self::assertNotEmpty($createdEvents);
        $createdEvent = array_shift($createdEvents);
        self::assertEquals($id, $createdEvent->id);
        self::assertEquals($user, $createdEvent->user);
        self::assertEquals($type, $createdEvent->type);
    }

    private function owner(): User
    {
        return (new UserBuilder())->build();
    }
}
