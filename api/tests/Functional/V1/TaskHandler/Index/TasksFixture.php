<?php

declare(strict_types=1);

namespace App\Test\Functional\V1\TaskHandler\Index;

use App\Auth\Entity\User\User;
use App\TaskHandler\Entity\Author\Author;
use App\TaskHandler\Entity\Author\Email;
use App\TaskHandler\Entity\Author\Id;
use App\TaskHandler\Entity\Task\Position;
use App\TaskHandler\Test\Builder\TaskBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class TasksFixture extends AbstractFixture
{
    public array $taskNames = ['Task1', 'Task2', 'Task3'];

    public function load(ObjectManager $manager): void
    {
        foreach ($this->taskNames as $taskName) {
            $task = (new TaskBuilder())
                ->withAuthor($this->createAuthor())
                ->withName($taskName)
                ->build();

            $position = new Position($task);

            $manager->persist($task);
            $manager->persist($position);
        }

        $manager->flush();
    }

    public function tasksCount(): int
    {
        return count($this->taskNames);
    }

    private function createAuthor(): Author
    {
        /** @var User $user */
        $user = $this->getReference('user');

        return new Author(
            new Id($user->getId()->getValue()),
            new Email($user->getEmail()->getValue())
        );
    }
}
