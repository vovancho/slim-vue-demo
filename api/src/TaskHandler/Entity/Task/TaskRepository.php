<?php

declare(strict_types=1);

namespace App\TaskHandler\Entity\Task;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;
use DomainException;

class TaskRepository
{
    /**
     * @var ObjectRepository|EntityRepository
     */
    private ObjectRepository $repo;
    /**
     * @var ObjectRepository|EntityRepository
     */
    private ObjectRepository $repoPosition;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(Task::class);
        $this->repoPosition = $em->getRepository(Position::class);
        $this->em = $em;
    }

    public function get(Id $id, $force = false): Task
    {
        /** @var Task $task */
        if (!$task = $this->repo->find($id->getValue())) {
            throw new DomainException('Задача не найдена.');
        }
        if ($force) {
            $this->em->refresh($task);
        }
        return $task;
    }

    public function add(Task $task, Position $position): void
    {
        $this->em->persist($task);
        $this->em->persist($position);
    }

    public function getPosition(Task $task): Position
    {
        /** @var Position $position */
        if (!$position = $this->repoPosition->find($task->getId()->getValue())) {
            throw new DomainException('Позиция не найдена.');
        }
        return $position;
    }

    public function resetPosition(Position $position): void
    {
        $this->em->remove($position);
    }
}
