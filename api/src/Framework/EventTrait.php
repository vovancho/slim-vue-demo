<?php

declare(strict_types=1);

namespace App\Framework;

trait EventTrait
{
    private array $recordedEvents = [];

    public function recordEvent($event): void
    {
        $this->recordedEvents[] = $event;
    }

    public function releaseEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];
        return $events;
    }
}
