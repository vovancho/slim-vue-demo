<?php

declare(strict_types=1);

namespace Api\Data\Fixture;

use Api\Model\Task\Entity\Task\Position;
use Api\Model\User\Entity\User\User;
use Api\Test\Builder\Task\TaskBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class TasksFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /** @var User $user */
        $user = $this->getReference('user');

        for ($i = 1; $i <= 20; $i++) {
            $task = (new TaskBuilder())
                ->withUser($user)
                ->withName("Task$i")
                ->build();

            $manager->persist($task);

            $position = new Position($task);
            $manager->persist($position);
        }

//        $manager->flush();
    }

    public function getOrder()
    {
        return 10;
    }
}
