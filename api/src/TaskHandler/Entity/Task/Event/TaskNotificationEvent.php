<?php

declare(strict_types=1);

namespace App\TaskHandler\Entity\Task\Event;

use App\TaskHandler\Entity\Author\Author;
use App\TaskHandler\Entity\Task\Visibility;

class TaskNotificationEvent
{
    public Visibility $visibility;
    public Author $author;

    public function __construct(Visibility $visibility, Author $author)
    {
        $this->visibility = $visibility;
        $this->author = $author;
    }
}
