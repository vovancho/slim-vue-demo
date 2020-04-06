<?php

declare(strict_types=1);

namespace App\TaskHandler\Test\Unit\Entity\Task\Task;

use App\TaskHandler\Entity\Author\Author;
use App\TaskHandler\Entity\Author\Email;
use App\TaskHandler\Entity\Task\Event\TaskCreated;
use App\TaskHandler\Entity\Task\Id;
use App\TaskHandler\Entity\Task\Visibility;
use App\TaskHandler\Test\Builder\AuthorBuilder;
use App\TaskHandler\Test\Builder\TaskBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\TaskHandler\Entity\Task\Task
 */
class CreateTest extends TestCase
{
    public function testSuccess(): void
    {
        $task = (new TaskBuilder())
            ->withId($id = Id::generate())
            ->withPushedAt($pushedAt = new \DateTimeImmutable())
            ->withAuthor($author = $this->createAuthor(new Email('author@example.com')))
            ->withVisibility($visibility = Visibility::public())
            ->withName($name = 'Task1')
            ->build();

        self::assertEquals($id, $task->getId());
        self::assertEquals($pushedAt, $task->getPushedAt());
        self::assertEquals($author, $task->getAuthor());
        self::assertEquals($visibility, $task->getVisibility());
        self::assertEquals($name, $task->getName());
        self::assertEquals(0, $task->getProcessPercent());
        self::assertNull($task->getError());

        self::assertTrue($task->isWait());
        self::assertFalse($task->isExecute());
        self::assertFalse($task->isComplete());
        self::assertFalse($task->isInterrupted());
        self::assertFalse($task->isCancel());
        self::assertFalse($task->isError());

        $createdEvents = array_filter($task->releaseEvents(), fn ($class) => get_class($class) === TaskCreated::class);
        self::assertNotEmpty($createdEvents);
        $createdEvent = array_shift($createdEvents);
        self::assertEquals($id, $createdEvent->id);
        self::assertEquals($author, $createdEvent->author);
        self::assertEquals($visibility, $createdEvent->visibility);
    }

    private function createAuthor(Email $email): Author
    {
        return (new AuthorBuilder())
            ->withEmail($email)
            ->build();
    }
}
