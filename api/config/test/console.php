<?php

declare(strict_types=1);

use Doctrine\Migrations;
use Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand;
use Laminas\Stdlib\ArrayUtils\MergeReplaceKey;

return [
    'config' => [
        'console' => [
            'commands' => new MergeReplaceKey([
                ValidateSchemaCommand::class,
                Migrations\Tools\Console\Command\MigrateCommand::class,
            ]),
        ],
    ],
];
