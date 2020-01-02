<?php

declare(strict_types=1);


use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return [
    LoggerInterface::class => function(ContainerInterface $container) {
        $config = $container->get('config')['logger'];
        $logger = new \Monolog\Logger('API');
        $logger->pushHandler(new \Monolog\Handler\StreamHandler($config['file']));
        return $logger;
    },

    'config' => [
        'logger' => [
            'file' => 'var/log/app.log',
        ]
    ]
];