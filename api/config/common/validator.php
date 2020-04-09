<?php

declare(strict_types=1);

use Doctrine\Common\Annotations\AnnotationRegistry;
use Psr\Container\ContainerInterface;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

return [
    ValidatorInterface::class => function (ContainerInterface $container) {
        AnnotationRegistry::registerLoader('class_exists');

        $validatorBuilder = Validation::createValidatorBuilder();

        if ($container->has(TranslatorInterface::class)) {
            $params = $container->get('config')['validator'];
            $translatorParams = $container->get('config')['translator'];
            $translation_file = preg_replace('/%locale%/', $translatorParams['locale'], $params['translation_file']);

            $translator = $container->get(TranslatorInterface::class);
            $translator->addLoader('xlf', new XliffFileLoader());
            $translator->addResource('xlf', $translation_file, $translatorParams['locale'], 'validators');

            $validatorBuilder
                ->setTranslator($translator)
                ->setTranslationDomain('validators');
        }

        return $validatorBuilder
            ->enableAnnotationMapping()
            ->getValidator();
    },
    'config' => [
        'validator' => [
            'translation_file' => __DIR__
                . '/../../vendor/symfony/validator/Resources/translations/validators.%locale%.xlf',
        ],
    ],
];
