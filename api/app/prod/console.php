<?php


use Api\Console\Command;

return [
    Command\Task\ProcessCommand::class => \DI\autowire(Command\Task\ProcessCommand::class),

    'config' => [
        'console' => [
            'commands' => [
                Command\Task\ProcessCommand::class,
            ],
        ],
    ],
];
