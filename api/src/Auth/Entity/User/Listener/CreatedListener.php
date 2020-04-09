<?php

declare(strict_types=1);

namespace App\Auth\Entity\User\Listener;

use App\Auth\Entity\User\Event\UserCreated;
use App\Auth\Service\ConfirmationSender;

class CreatedListener
{
    private ConfirmationSender $sender;

    public function __construct(ConfirmationSender $sender)
    {
        $this->sender = $sender;
    }

    public function __invoke(UserCreated $event)
    {
        $this->sender->send($event->email, $event->confirmToken);
    }
}
