<?php

declare(strict_types=1);

namespace App\Auth\Service;

use App\Auth\Entity\User\Token;
use DateInterval;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

class Tokenizer
{
    private DateInterval $interval;

    public function __construct(DateInterval $interval)
    {
        $this->interval = $interval;
    }

    public function generate(DateTimeImmutable $date): Token
    {
        return new Token(
            (string)rand(100000, 999999),
            $date->add($this->interval)
        );
    }
}
