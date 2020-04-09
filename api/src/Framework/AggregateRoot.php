<?php

declare(strict_types=1);

namespace App\Framework;

interface AggregateRoot
{
    public function releaseEvents(): array;
}
