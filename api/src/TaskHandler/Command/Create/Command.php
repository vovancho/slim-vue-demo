<?php

namespace App\TaskHandler\Command\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\Uuid()
     */
    public string $author;
    /**
     * @Assert\NotBlank()
     * @Assert\Choice(callback={"App\TaskHandler\Entity\Task\Visibility", "getItems"})
     */
    public string $visibility;
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    public string $name;
}
