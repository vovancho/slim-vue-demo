<?php

declare(strict_types=1);

namespace Api\Infrastructure\ReadModel\Task;


use Api\ReadModel\PaginationInterface;
use Api\ReadModel\Task\TaskReadRepository;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineTaskReadRepository implements TaskReadRepository
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function countByUser(string $userId): int
    {
        return $this->queryByUser($userId)->execute()->rowCount();
    }

    public function allByUser(string $userId, PaginationInterface $pagination): array
    {
        $query = $this->queryByUser($userId)
            ->setFirstResult($pagination->offset())
            ->setMaxResults($pagination->itemsPerPage());

        $this->setSort($query, $pagination);

        return $query->execute()->fetchAll();
    }

    private function queryByUser(string $userId): QueryBuilder
    {
        $subPosition = $this->em->getConnection()->createQueryBuilder()
            ->select(['p.task_id', 'row_number() OVER (ORDER BY task_id) AS position'])
            ->from('task_positions', 'p');

        return $this->em->getConnection()->createQueryBuilder()
            ->select(['t.id', 't.pushed_at', 't.user_id', 'u.email AS user_email', 't.type', 't.name', 't.status', 't.process_percent', 't.error_message', 'p.position'])
            ->from('task_tasks', 't')
            ->leftJoin('t', sprintf('(%s)', $subPosition->getSQL()), 'p', 't.id = p.task_id')
            ->leftJoin('t', 'user_users', 'u', 't.user_id = u.id')
            ->andWhere('t.user_id = :user')
            ->setParameter(':user', $userId);
    }

    private function setSort(QueryBuilder $query, PaginationInterface $pagination): void
    {
        if ($pagination->hasSort() && $this->sortValidate($pagination->sortBy())) {
            foreach ($pagination->sortBy() as $sortByAttr) {
                $query->addOrderBy($sortByAttr, $pagination->orderByAttr($sortByAttr));
            }
        } else {
            $query->orderBy('id', 'DESC');
        }
    }

    private function sortValidate(array $sortBy): bool
    {
        foreach ($sortBy as $sortByAttr) {
            if (!in_array($sortByAttr, ['id', 'pushed_at', 'user_email', 'type', 'name', 'status', 'process_percent'])) {
                return false;
            }
        }
        return true;
    }
}
