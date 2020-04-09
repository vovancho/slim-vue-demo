<?php

declare(strict_types=1);

namespace App\Auth\Command\SignUp\Confirm;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public string $email;
    /**
     * @Assert\NotBlank()
     */
    public string $token;
}