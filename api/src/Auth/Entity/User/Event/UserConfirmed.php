<?php

declare(strict_types=1);

namespace App\Auth\Entity\User\Event;

use App\Auth\Entity\User\Id;

class UserConfirmed
{
    public Id $id;

    public function __construct(Id $id)
    {
        $this->id = $id;
    }
}
