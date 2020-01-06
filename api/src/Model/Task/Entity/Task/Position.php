<?php

declare(strict_types=1);

namespace Api\Model\Task\Entity\Task;

use Api\Model\Base\Uuid1;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="task_positions")
 */
class Position
{
    /**
     * @var Uuid1
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Api\Model\Task\Entity\Task\Task")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }
}
