<?php

declare(strict_types=1);

namespace Api\Infrastructure\Model\Task\Entity;


use Api\Model\Base\Uuid1;
use Api\Model\EntityNotFoundException;
use Api\Model\Task\Entity\Task\Position;
use Api\Model\Task\Entity\Task\Task;
use Api\Model\Task\Entity\Task\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use PDO;

class DoctrineTaskRepository implements TaskRepository
{
    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $repo;
    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $repoPosition;
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(Task::class);
        $this->repoPosition = $em->getRepository(Position::class);
        $this->em = $em;
    }

    public function get(Uuid1 $id, $force = false): Task
    {
        /** @var Task $task */
        if (!$task = $this->repo->find($id->getId())) {
            throw new EntityNotFoundException('Задача не найдена.');
        }
        if ($force) {
            $this->em->refresh($task);
        }
        return $task;
    }

    public function getPosition(Task $task): Position
    {
        /** @var Position $position */
        if (!$position = $this->repoPosition->find($task->getId()->getId())) {
            throw new EntityNotFoundException('Позиция не найдена.');
        }
        return $position;
    }

    public function position(Uuid1 $id): int
    {
        $subPosition = $this->em->getConnection()->createQueryBuilder()
            ->select(['p.task_id', 'row_number() OVER (ORDER BY task_id) AS position'])
            ->from('task_positions', 'p');

        $result = $this->em->getConnection()->createQueryBuilder()
            ->select(['p.position'])
            ->from('task_tasks', 't')
            ->leftJoin('t', sprintf('(%s)', $subPosition->getSQL()), 'p', 't.id = p.task_id')
            ->andWhere('t.id = :task')
            ->setParameter(':task', $id->getId())
            ->execute()->fetch(PDO::FETCH_ASSOC);

        if (empty($result)) {
            return 0;
        }
        return $result['position'] > 0 ? $result['position'] - 1 : 0;
    }

    public function add(Task $task, Position $position): void
    {
        $this->em->persist($task);
        $this->em->persist($position);
    }

    public function resetPosition(Position $position): void
    {
        $this->em->remove($position);
    }
}
