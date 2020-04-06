<?php

use App\Console\Command;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Migrations;
use Doctrine\ORM\Tools\Console\Command\SchemaTool;
use Psr\Container\ContainerInterface;

return [
    Command\Task\ProcessCommand::class => \DI\autowire(Command\Task\ProcessCommand::class),
    Command\FixtureCommand::class => static function (ContainerInterface $container) {
        $config = $container->get('config')['console'];

        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);

        return new Command\FixtureCommand(
            $em,
            $config['fixture_paths']
        );
    },

    'config' => [
        'console' => [
            'commands' => [
                Command\FixtureCommand::class,

                SchemaTool\DropCommand::class,

                Migrations\Tools\Console\Command\DiffCommand::class,
                Migrations\Tools\Console\Command\GenerateCommand::class,
            ],
            'fixture_paths' => [
                __DIR__ . '/../../src/Data/Fixture',
            ],
        ],
    ],
];
