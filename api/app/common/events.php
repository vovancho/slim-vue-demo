<?php

declare(strict_types=1);

use Api\Infrastructure\Model\EventDispatcher\Listener;
use Api\Infrastructure\Model\EventDispatcher\SyncEventDispatcher;
use Api\Model\User as UserModel;
use Api\Model\Task as TaskModel;
use Psr\Container\ContainerInterface;

return [
    Api\Model\EventDispatcher::class => function (ContainerInterface $container) {
        return new SyncEventDispatcher(
            $container,
            [
                UserModel\Entity\User\Event\UserCreated::class => [
                    Listener\User\CreatedListener::class,
                ],

                TaskModel\Entity\Task\Event\TaskCreated::class => [
                    Listener\Task\Job\CreatedListener::class,
                    Listener\Task\Notification\CreatedListener::class,
                ],
                TaskModel\Entity\Task\Event\TaskExecuted::class => [
                    Listener\Task\Notification\ExecutedListener::class,
                ],
                TaskModel\Entity\Task\Event\TaskProcessed::class => [
                    Listener\Task\Notification\ProcessedListener::class,
                ],
                TaskModel\Entity\Task\Event\TaskCompleted::class => [
                    Listener\Task\Notification\CompletedListener::class,
                ],
                TaskModel\Entity\Task\Event\TaskCanceled::class => [
                    Listener\Task\Notification\CanceledListener::class,
                ],
                TaskModel\Entity\Task\Event\TaskError::class => [
                    Listener\Task\Notification\ErrorListener::class,
                ],
            ]
        );
    },

    Listener\User\CreatedListener::class => function (ContainerInterface $container) {
        return new Listener\User\CreatedListener(
            $container->get(Swift_Mailer::class),
            $container->get('config')['mailer']['from']
        );
    },

    Listener\Task\Job\CreatedListener::class => \DI\autowire(Listener\Task\Job\CreatedListener::class),
    Listener\Task\Notification\CreatedListener::class => \DI\autowire(Listener\Task\Notification\CreatedListener::class),
    Listener\Task\Notification\ExecutedListener::class => \DI\autowire(Listener\Task\Notification\ExecutedListener::class),
    Listener\Task\Notification\ProcessedListener::class => \DI\autowire(Listener\Task\Notification\ProcessedListener::class),
    Listener\Task\Notification\CompletedListener::class => \DI\autowire(Listener\Task\Notification\CompletedListener::class),
    Listener\Task\Notification\CanceledListener::class => \DI\autowire(Listener\Task\Notification\CanceledListener::class),
    Listener\Task\Notification\ErrorListener::class => \DI\autowire(Listener\Task\Notification\ErrorListener::class),
];
