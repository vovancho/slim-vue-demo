<?php

declare(strict_types=1);

namespace App\Auth\Entity\User\Event;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Token;

class UserCreated
{
    public Id $id;
    public Email $email;
    public Token $confirmToken;

    public function __construct(Id $id, Email $email, Token $confirmToken)
    {
        $this->id = $id;
        $this->email = $email;
        $this->confirmToken = $confirmToken;
    }
}
