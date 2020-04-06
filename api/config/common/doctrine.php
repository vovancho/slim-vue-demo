<?php

declare(strict_types=1);

use App\Auth;
use App\TaskHandler;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\EventManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\Tools\Setup;
use Psr\Container\ContainerInterface;

return [
    EntityManagerInterface::class => function (ContainerInterface $container): EntityManagerInterface {
        $params = $container->get('config')['doctrine'];

        $config = Setup::createAnnotationMetadataConfiguration(
            $params['metadata_dirs'],
            $params['dev_mode'],
            $params['proxy_dir'],
            $params['cache_dir'] ? new FilesystemCache($params['cache_dir']) : new ArrayCache(),
            false
        );

        $config->setNamingStrategy(new UnderscoreNamingStrategy());

        foreach ($params['types'] as $name => $class) {
            if (!Type::hasType($name)) {
                Type::addType($name, $class);
            }
        }

        $eventManager = new EventManager();

        foreach ($params['subscribers'] as $name) {
            /** @var EventSubscriber $subscriber */
            $subscriber = $container->get($name);
            $eventManager->addEventSubscriber($subscriber);
        }

        return EntityManager::create(
            $params['connection'],
            $config,
            $eventManager
        );
    },

    'config' => [
        'doctrine' => [
            'dev_mode' => false,
            'cache_dir' => __DIR__ . '/../../var/cache/doctrine/cache',
            'proxy_dir' => __DIR__ . '/../../var/cache/doctrine/proxy',
            'connection' => [
                'driver' => 'pdo_pgsql',
                'host' => getenv('API_DB_HOST'),
                'user' => getenv('API_DB_USER'),
                'password' => getenv('API_DB_PASSWORD'),
                'dbname' => getenv('API_DB_NAME'),
                'charset' => 'utf-8'
            ],
            'subscribers' => [],
            'metadata_dirs' => [
                __DIR__ . '/../../src/Auth/Entity',
                __DIR__ . '/../../src/TaskHandler/Entity',
            ],
            'types' => [
                Auth\Entity\User\IdType::NAME => Auth\Entity\User\IdType::class,
                Auth\Entity\User\EmailType::NAME => Auth\Entity\User\EmailType::class,
                Auth\Entity\User\StatusType::NAME => Auth\Entity\User\StatusType::class,

                Auth\Entity\OAuth\ClientType::NAME => Auth\Entity\OAuth\ClientType::class,
                Auth\Entity\OAuth\ScopesType::NAME => Auth\Entity\OAuth\ScopesType::class,

                TaskHandler\Entity\Author\IdType::NAME => TaskHandler\Entity\Author\IdType::class,
                TaskHandler\Entity\Author\EmailType::NAME => TaskHandler\Entity\Author\EmailType::class,

                TaskHandler\Entity\Task\IdType::NAME => TaskHandler\Entity\Task\IdType::class,
                TaskHandler\Entity\Task\StatusType::NAME => TaskHandler\Entity\Task\StatusType::class,
                TaskHandler\Entity\Task\VisibilityType::NAME => TaskHandler\Entity\Task\VisibilityType::class,
            ],
        ],
    ],
];
