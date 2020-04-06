<?php

declare(strict_types=1);

use App\EventDispatcher\SyncEventDispatcher;
use App\TaskHandler\Entity\Task\{
    Event\TaskCreated,
    Event\TaskExecuted,
    Event\TaskProcessed,
    Event\TaskCompleted,
    Event\TaskCanceled,
    Event\TaskError,
};
use App\TaskHandler\Entity\Task\Listener\{
    Job\CreatedListener as JobCreatedListener,
    Notification\CreatedListener as NotificationCreatedListener,
    Notification\ExecutedListener,
    Notification\ProcessedListener,
    Notification\CompletedListener,
    Notification\CanceledListener,
    Notification\ErrorListener,
};
use App\Auth\Entity\User;
use Psr\Container\ContainerInterface;

return [
    SyncEventDispatcher::class => function (ContainerInterface $container) {
        return new SyncEventDispatcher(
            $container,
            [
                User\Event\UserCreated::class => [
                    User\Listener\CreatedListener::class,
                ],

                TaskCreated::class => [
                    JobCreatedListener::class,
                    NotificationCreatedListener::class,
                ],
                TaskExecuted::class => [
                    ExecutedListener::class,
                ],
                TaskProcessed::class => [
                    ProcessedListener::class,
                ],
                TaskCompleted::class => [
                    CompletedListener::class,
                ],
                TaskCanceled::class => [
                    CanceledListener::class,
                ],
                TaskError::class => [
                    ErrorListener::class,
                ],
            ]
        );
    },

    User\Listener\CreatedListener::class => \DI\autowire(User\Listener\CreatedListener::class),

    JobCreatedListener::class => \DI\autowire(JobCreatedListener::class),
    NotificationCreatedListener::class => \DI\autowire(NotificationCreatedListener::class),
    ExecutedListener::class => \DI\autowire(ExecutedListener::class),
    ProcessedListener::class => \DI\autowire(ProcessedListener::class),
    CompletedListener::class => \DI\autowire(CompletedListener::class),
    CanceledListener::class => \DI\autowire(CanceledListener::class),
    ErrorListener::class => \DI\autowire(ErrorListener::class),
];
