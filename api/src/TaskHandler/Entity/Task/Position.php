<?php

declare(strict_types=1);

namespace App\TaskHandler\Entity\Task;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="task_handler_positions")
 */
class Position
{
    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="App\TaskHandler\Entity\Task\Task")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private Task $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }
}
