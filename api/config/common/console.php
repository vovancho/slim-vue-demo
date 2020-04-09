<?php

declare(strict_types=1);

use Doctrine\Migrations;
use Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand;
use App\Console\Command;

return [
    Command\Task\ProcessCommand::class => \DI\autowire(Command\Task\ProcessCommand::class),
    'config' => [
        'console' => [
            'commands' => [
                Command\Task\ProcessCommand::class,

                ValidateSchemaCommand::class,

                Migrations\Tools\Console\Command\ExecuteCommand::class,
                Migrations\Tools\Console\Command\MigrateCommand::class,
                Migrations\Tools\Console\Command\LatestCommand::class,
                Migrations\Tools\Console\Command\StatusCommand::class,
                Migrations\Tools\Console\Command\UpToDateCommand::class,
            ],
        ],
    ],
];
