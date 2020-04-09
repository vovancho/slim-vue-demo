<?php

declare(strict_types=1);

namespace App\TaskHandler\Command\Execute;

use App\TaskHandler\Entity\Task\Id;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\Uuid()
     */
    public Id $id;
}
