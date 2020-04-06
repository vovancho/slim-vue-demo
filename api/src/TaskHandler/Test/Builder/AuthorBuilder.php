<?php

declare(strict_types=1);

namespace App\TaskHandler\Test\Builder;

use App\TaskHandler\Entity\Author\Author;
use App\TaskHandler\Entity\Author\Email;
use App\TaskHandler\Entity\Author\Id;

class AuthorBuilder
{
    private Id $id;
    private Email $email;

    public function __construct()
    {
        $this->id = Id::generate();
        $this->email = new Email('mail@example.com');
    }

    public function withEmail(Email $email): self
    {
        $clone = clone $this;
        $clone->email = $email;
        return $clone;
    }

    public function build(): Author
    {
        return new Author(
            $this->id,
            $this->email,
        );
    }
}
