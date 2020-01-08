<?php

declare(strict_types=1);

namespace Api\Model\Task\Entity\Task;

use Api\Model\Base\Uuid1;

interface TaskRepository
{
    public function get(Uuid1 $id): Task;

    public function add(Task $task, Position $position): void;

    public function resetPosition(Position $position): void;

    public function position(Uuid1 $id): int;

    public function getPosition(Task $task): Position;
}
