<?php

declare(strict_types=1);

namespace App;

use App\EventDispatcher\SyncEventDispatcher;
use App\Framework\AggregateRoot;
use Doctrine\ORM\EntityManagerInterface;

class Flusher
{
    private EntityManagerInterface $em;
    private SyncEventDispatcher $dispatcher;

    public function __construct(EntityManagerInterface $em, SyncEventDispatcher $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    public function flush(AggregateRoot ...$roots): void
    {
        $this->em->flush();

        $events = array_reduce($roots, function (array $events, AggregateRoot $root) {
            return array_merge($events, $root->releaseEvents());
        }, []);

        $this->dispatcher->dispatch(...$events);
    }
}
