<?php

declare(strict_types=1);

namespace App\TaskHandler\Entity\Task\Event;

use App\TaskHandler\Entity\Author\Author;
use App\TaskHandler\Entity\Task\Task;
use App\TaskHandler\Entity\Task\Visibility;

class TaskProcessed extends TaskNotificationEvent
{
    public Task $task;

    public function __construct(Visibility $visibility, Author $author, Task $task)
    {
        parent::__construct($visibility, $author);
        $this->task = $task;
    }
}
