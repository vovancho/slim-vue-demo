<?php

use Api\Console\Command;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

return [
    Command\Task\ProcessCommand::class => \DI\autowire(Command\Task\ProcessCommand::class),
    Command\FixtureCommand::class => function (ContainerInterface $container) {
        return new Command\FixtureCommand(
            $container->get(EntityManagerInterface::class),
            'src/Data/Fixture'
        );
    },

    'config' => [
        'console' => [
            'commands' => [
                Command\FixtureCommand::class,
                Command\Task\ProcessCommand::class,
            ],
        ],
    ],
];
