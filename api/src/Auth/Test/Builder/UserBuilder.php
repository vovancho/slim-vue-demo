<?php

declare(strict_types=1);

namespace App\Auth\Test\Builder;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use DateTimeImmutable;

class UserBuilder
{
    private Id $id;
    private DateTimeImmutable $date;
    private Email $email;
    private string $hash;
    private Token $confirmToken;
    private bool $active = false;

    public function __construct()
    {
        $this->id = Id::generate();
        $this->date = new DateTimeImmutable();
        $this->email = new Email('mail@example.com');
        $this->hash = 'hash';
        $this->confirmToken = new Token((string)rand(100000, 999999), $this->date->modify('+1 day'));
    }

    public function withEmail(Email $email): self
    {
        $clone = clone $this;
        $clone->email = $email;
        return $clone;
    }

    public function withConfirmToken(Token $token): self
    {
        $clone = clone $this;
        $clone->confirmToken = $token;
        return $clone;
    }

    public function active(): self
    {
        $clone = clone $this;
        $clone->active = true;
        return $clone;
    }

    public function build(): User
    {
        $user = User::requestForConfirm(
            $this->id,
            $this->date,
            $this->email,
            $this->hash,
            $this->confirmToken
        );

        if ($this->active) {
            $user->confirmSignUp(
                $this->confirmToken->getValue(),
                $this->confirmToken->getExpires()->modify('-1 day')
            );
        }

        return $user;
    }
}
