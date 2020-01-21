<?php

declare(strict_types=1);

namespace Api\Model\Task\Entity\Task\Event;


use Api\Model\Base\Uuid1;
use Api\Model\User\Entity\User\User;

class TaskCreated extends TaskNotificationEvent
{
    public $id;

    public function __construct(string $type, User $user, Uuid1 $id)
    {
        parent::__construct($type, $user);
        $this->id = $id;
    }
}
