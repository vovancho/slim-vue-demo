<?php

declare(strict_types=1);

use Api\Infrastructure;
use Api\Infrastructure\Model\User as UserInfrastructure;
use Api\Infrastructure\Model\Task as TaskInfrastructure;
use Api\Model\User as UserModel;
use Api\Model\Task as TaskModel;
use Api\ReadModel;
use Psr\Container\ContainerInterface;

return [
    UserModel\Service\ConfirmTokenizer::class => function (ContainerInterface $container) {
        $interval = $container->get('config')['auth']['signup_confirm_interval'];
        return new UserInfrastructure\Service\RandConfirmTokenizer(new \DateInterval($interval));
    },

    Api\Model\Flusher::class => \DI\autowire(Api\Infrastructure\Model\Service\DoctrineFlusher::class),
    UserModel\Service\PasswordHasher::class => \DI\autowire(UserInfrastructure\Service\BCryptPasswordHasher::class),

    UserModel\UseCase\SignUp\Request\Handler::class => \DI\autowire(UserModel\UseCase\SignUp\Request\Handler::class),
    UserModel\UseCase\SignUp\Confirm\Handler::class => \DI\autowire(UserModel\UseCase\SignUp\Confirm\Handler::class),

    UserModel\Entity\User\UserRepository::class => \DI\autowire(UserInfrastructure\Entity\DoctrineUserRepository::class),
    TaskModel\Entity\Task\TaskRepository::class => \DI\autowire(TaskInfrastructure\Entity\DoctrineTaskRepository::class),

    ReadModel\User\UserReadRepository::class => \DI\autowire(Infrastructure\ReadModel\User\DoctrineUserReadRepository::class),
    ReadModel\Task\TaskReadRepository::class => \DI\autowire(Infrastructure\ReadModel\Task\DoctrineTaskReadRepository::class),

    'config' => [
        'auth' => [
            'signup_confirm_interval' => 'PT5M',
        ],
    ],
];
