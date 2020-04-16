<?php

declare(strict_types=1);

use Middlewares\ContentLanguage;
use Psr\Container\ContainerInterface;
use Symfony\Component\Translation\Loader\PhpFileLoader;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

return [
    TranslatorInterface::class => DI\get(Translator::class),

    Translator::class => function (ContainerInterface $container): Translator {
        $config = $container->get('config')['translator'];

        $translator = new Translator($config['lang']);
        $translator->addLoader('php', new PhpFileLoader());
        $translator->addLoader('xlf', new XliffFileLoader());

        foreach ($config['resources'] as $resource) {
            $translator->addResource(...$resource);
        }

        return $translator;
    },

    ContentLanguage::class => function (ContainerInterface $container): ContentLanguage {
        $config = $container->get('config')['locales'];

        return new ContentLanguage($config['allowed']);
    },

    'config' => [
        'translator' => [
            'lang' => 'ru',
            'resources' => [
                [
                    'xlf',
                    __DIR__ . '/../../vendor/symfony/validator/Resources/translations/validators.ru.xlf',
                    'ru',
                    'validators',
                ],
                [
                    'php',
                    __DIR__ . '/../../translations/exceptions.en.php',
                    'en',
                    'exceptions',
                ],
            ],
        ],
        'locales' => [
            'allowed' => ['ru', 'en'],
        ],
    ],
];
