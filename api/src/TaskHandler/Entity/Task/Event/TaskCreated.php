<?php

declare(strict_types=1);

namespace App\TaskHandler\Entity\Task\Event;

use App\TaskHandler\Entity\Author\Author;
use App\TaskHandler\Entity\Task\Id;
use App\TaskHandler\Entity\Task\Visibility;

class TaskCreated extends TaskNotificationEvent
{
    public Id $id;

    public function __construct(Visibility $visibility, Author $user, Id $id)
    {
        parent::__construct($visibility, $user);
        $this->id = $id;
    }
}
