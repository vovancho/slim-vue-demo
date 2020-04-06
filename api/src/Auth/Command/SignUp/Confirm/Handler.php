<?php

declare(strict_types=1);

namespace App\Auth\Command\SignUp\Confirm;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\UserRepository;
use App\Flusher;
use DateTimeImmutable;

class Handler
{
    private UserRepository $users;
    private Flusher $flusher;

    public function __construct(UserRepository $users, Flusher $flusher)
    {
        $this->users = $users;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $user = $this->users->getByEmail(new Email($command->email));

        $user->confirmSignup($command->token, new DateTimeImmutable());

        $this->flusher->flush($user);
    }
}
