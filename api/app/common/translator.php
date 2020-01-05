<?php

declare(strict_types=1);


use Psr\Container\ContainerInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

return [
    TranslatorInterface::class => function (ContainerInterface $container) {
        $params = $container->get('config')['translator'];
        return new Translator($params['locale']);
    },
    'config' => [
        'translator' => [
            'locale' => 'ru',
        ],
    ],
];
