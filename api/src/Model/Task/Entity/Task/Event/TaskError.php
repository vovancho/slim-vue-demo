<?php

declare(strict_types=1);

namespace Api\Model\Task\Entity\Task\Event;


use Api\Model\Task\Entity\Task\Task;
use Api\Model\User\Entity\User\User;

class TaskError
{
    public $task;
    public $type;
    public $user;

    public function __construct(Task $task, string $type, User $user)
    {
        $this->task = $task;
        $this->type = $type;
        $this->user = $user;
    }
}
