<?php

declare(strict_types=1);

namespace Api\Test\Feature\Task\Index;

use Api\Model\Task\Entity\Task\Position;
use Api\Model\User\Entity\User\User;
use Api\Test\Builder\Task\TaskBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class TasksFixture extends AbstractFixture
{
    public $taskNames = ['Task1', 'Task2', 'Task3'];

    public function load(ObjectManager $manager): void
    {
        /** @var User $user */
        $user = $this->getReference('user');

        foreach ($this->taskNames as $taskName) {
            $task = (new TaskBuilder())
                ->withUser($user)
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
}
