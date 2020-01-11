<?php

declare(strict_types=1);

namespace Api\Model\Task\Entity\Task\Event;


use Api\Model\Base\Uuid1;
use Api\Model\User\Entity\User\User;

class TaskExecuted
{
    public $id;
    public $user;

    public function __construct(Uuid1 $id, User $user)
    {
        $this->id = $id;
        $this->user = $user;
    }
}
