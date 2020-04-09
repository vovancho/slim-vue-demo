<?php

declare(strict_types=1);

use Finesse\SwiftMailerDefaultsPlugin\SwiftMailerDefaultsPlugin;
use Psr\Container\ContainerInterface;

return [
    Swift_Mailer::class => static function (ContainerInterface $container) {
        $config = $container->get('config')['mailer'];

        $transport = (new Swift_SmtpTransport($config['host'], $config['port']))
            ->setUsername($config['username'])
            ->setPassword($config['password'])
            ->setEncryption($config['encryption']);

        $mailer = new Swift_Mailer($transport);

        $mailer->registerPlugin(new SwiftMailerDefaultsPlugin([
            'from' => $config['from'],
        ]));

        return $mailer;
    },

    'config' => [
        'mailer' => [
            'host' => getenv('API_MAILER_HOST'),
            'port' => (int)getenv('API_MAILER_PORT'),
            'username' => getenv('API_MAILER_USERNAME'),
            'password' => getenv('API_MAILER_PASSWORD'),
            'encryption' => getenv('API_MAILER_ENCRYPTION'),
            'from' => [getenv('API_MAILER_FROM_EMAIL') => 'App'],
        ],
    ],
];
