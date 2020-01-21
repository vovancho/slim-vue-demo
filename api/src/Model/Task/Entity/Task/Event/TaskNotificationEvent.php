<?php

declare(strict_types=1);

namespace Api\Model\Task\Entity\Task\Event;


use Api\Model\User\Entity\User\User;

class TaskNotificationEvent
{
    public $type;
    public $user;

    public function __construct(string $type, User $user)
    {
        $this->type = $type;
        $this->user = $user;
    }
}
