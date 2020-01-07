<?php

declare(strict_types=1);

namespace Api\Infrastructure\Model\Task\Entity;


use Api\Model\Task\Entity\Task\Position;
use Api\Model\Task\Entity\Task\Task;
use Api\Model\Task\Entity\Task\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineTaskRepository implements TaskRepository
{
    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $repo;
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(Task::class);
        $this->em = $em;
    }

    public function add(Task $task, Position $position): void
    {
        $this->em->persist($task);
        $this->em->persist($position);
    }
}
