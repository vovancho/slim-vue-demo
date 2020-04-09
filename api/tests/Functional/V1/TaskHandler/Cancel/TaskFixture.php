<?php

declare(strict_types=1);

namespace App\Test\Functional\V1\TaskHandler\Cancel;

use App\Auth\Entity\User\User;
use App\TaskHandler\Entity\Author\Author;
use App\TaskHandler\Entity\Author\Email;
use App\TaskHandler\Entity\Author\Id;
use App\TaskHandler\Entity\Task\Position;
use App\TaskHandler\Entity\Task\Task;
use App\TaskHandler\Test\Builder\TaskBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class TaskFixture extends AbstractFixture
{
    private Task $task;

    public function load(ObjectManager $manager): void
    {
        $this->task = (new TaskBuilder())
            ->withAuthor($this->createAuthor())
            ->withName('Task1')
            ->build();

        $position = new Position($this->task);

        $manager->persist($this->task);
        $manager->persist($position);

        $manager->flush();
    }

    public function getTask(): Task
    {
        return $this->task;
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
