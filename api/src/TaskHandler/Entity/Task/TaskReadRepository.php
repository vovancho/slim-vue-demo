<?php

declare(strict_types=1);

namespace App\TaskHandler\Entity\Task;

use App\Framework\PaginationInterface;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;

class TaskReadRepository
{
    private EntityManagerInterface $em;

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

    private function queryByUser(string $authorId): QueryBuilder
    {
        $subPosition = $this->em->getConnection()->createQueryBuilder()
            ->select(['p.task_id', 'row_number() OVER (ORDER BY task_id) AS position'])
            ->from('task_handler_positions', 'p');

        return $this->em->getConnection()->createQueryBuilder()
            ->select([
                't.id',
                't.pushed_at',
                't.author_id',
                'u.email AS author_email',
                't.visibility',
                't.name',
                't.status',
                't.process_percent',
                't.error_message',
                't.error_trace',
                'p.position',
            ])
            ->from('task_handler_tasks', 't')
            ->leftJoin('t', sprintf('(%s)', $subPosition->getSQL()), 'p', 't.id = p.task_id')
            ->leftJoin('t', 'auth_users', 'u', 't.author_id = u.id')
            ->andWhere('t.visibility = :public_visibility')
            ->orWhere('t.author_id = :author AND t.visibility = :private_visibility')
            ->setParameter(':author', $authorId)
            ->setParameter(':public_visibility', Visibility::public()->getName())
            ->setParameter(':private_visibility', Visibility::private()->getName());
    }

    private function setSort(QueryBuilder $query, PaginationInterface $pagination): void
    {
        if ($pagination->hasSort() && $this->sortValidate($pagination->sortBy())) {
            foreach ($pagination->sortBy() as $sortByAttr) {
                $query->addOrderBy($sortByAttr, $pagination->orderByAttr($sortByAttr));
            }
        } else {
            $query->orderBy('pushed_at', 'DESC');
            $query->addOrderBy('id', 'DESC');
        }
    }

    private function sortValidate(array $sortBy): bool
    {
        foreach ($sortBy as $sortByAttr) {
            if (
                !in_array($sortByAttr, [
                    'id',
                    'pushed_at',
                    'author_email',
                    'visibility',
                    'name',
                    'status',
                    'process_percent',
                ])
            ) {
                return false;
            }
        }
        return true;
    }
}
