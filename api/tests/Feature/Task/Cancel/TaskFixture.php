<?php

declare(strict_types=1);

namespace Api\Test\Feature\Task\Cancel;

use Api\Model\Task\Entity\Task\Position;
use Api\Model\Task\Entity\Task\Task;
use Api\Model\User\Entity\User\User;
use Api\Test\Builder\Task\TaskBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class TaskFixture extends AbstractFixture
{
    private $task;

    public function load(ObjectManager $manager): void
    {
        /** @var User $user */
        $user = $this->getReference('user');

        $this->task = (new TaskBuilder())
            ->withUser($user)
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
}
