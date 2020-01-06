<?php

declare(strict_types=1);

namespace Api\Model\Task\Entity\Task;

interface TaskRepository
{
    public function add(Task $task): void;
}
