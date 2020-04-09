<?php

declare(strict_types=1);

namespace App\TaskHandler\Entity\Author;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Author
{
    /**
     * @ORM\Column(type="task_handler_author_id")
     */
    private Id $id;
    /**
     * @ORM\Column(type="task_handler_author_email")
     */
    private Email $email;

    public function __construct(Id $id, Email $email)
    {
        Assert::notEmpty($id);
        Assert::notEmpty($email);
        $this->id = $id;
        $this->email = $email;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }
}
