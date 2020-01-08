<?php

declare(strict_types=1);

namespace Api\Model\Task\Entity\Task\Event;


use Api\Model\Base\Uuid1;
use Api\Model\User\Entity\User\User;

class TaskCreated
{
    public $id;
    public $type;
    public $user;

    public function __construct(Uuid1 $id, string $type, User $user)
    {
        $this->id = $id;
        $this->type = $type;
        $this->user = $user;
    }
}
