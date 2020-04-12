<?php

declare(strict_types=1);

use App\EventDispatcher\SyncEventDispatcher;
use Psr\Container\ContainerInterface;

return [
    SyncEventDispatcher::class => function (ContainerInterface $container) {
        return new SyncEventDispatcher($container, []);
    },
];
