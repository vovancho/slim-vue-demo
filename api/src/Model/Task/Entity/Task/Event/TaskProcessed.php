<?php

declare(strict_types=1);

namespace Api\Model\Task\Entity\Task\Event;


use Api\Model\Task\Entity\Task\Task;
use Api\Model\User\Entity\User\User;

class TaskProcessed extends TaskNotificationEvent
{
    public $task;

    public function __construct(string $type, User $user, Task $task)
    {
        parent::__construct($type, $user);
        $this->task = $task;
    }
}
