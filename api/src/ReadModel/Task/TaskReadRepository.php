<?php

declare(strict_types=1);

namespace Api\ReadModel\Task;


use Api\ReadModel\PaginationInterface;

interface TaskReadRepository
{
    public function countByUser(string $userId): int;

    public function allByUser(string $userId, PaginationInterface $pagination): array;
}
