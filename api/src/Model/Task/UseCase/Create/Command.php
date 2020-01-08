<?php


namespace Api\Model\Task\UseCase\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public $user;
    /**
     * @Assert\NotBlank()
     * @Assert\Choice(callback={"Api\Model\Task\Entity\Task\Task", "getTypes"})
     */
    public $type;
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    public $name;
}
