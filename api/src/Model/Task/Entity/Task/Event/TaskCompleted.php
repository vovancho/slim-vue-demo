<?php

declare(strict_types=1);

namespace Api\Model\Task\Entity\Task\Event;


use Api\Model\Task\Entity\Task\Task;

class TaskCompleted
{
    public $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }
}
