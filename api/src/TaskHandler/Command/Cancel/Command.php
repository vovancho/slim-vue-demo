<?php

namespace App\TaskHandler\Command\Cancel;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\Uuid()
     */
    public string $id;
    /**
     * @Assert\Uuid()
     */
    public string $user;
}
