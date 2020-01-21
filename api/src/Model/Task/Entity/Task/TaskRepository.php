<?php

declare(strict_types=1);

namespace Api\Model\Task\Entity\Task;

use Api\Model\Base\Uuid1;

interface TaskRepository
{
    public function get(Uuid1 $id, $force = false): Task;

    public function add(Task $task, Position $position): void;

    public function getPosition(Task $task): Position;

    public function resetPosition(Position $position): void;
}
