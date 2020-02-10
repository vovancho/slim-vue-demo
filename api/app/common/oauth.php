<?php

declare(strict_types=1);

use League\OAuth2\Server;
use Api\Infrastructure\Model\OAuth as Infrastructure;
use Psr\Container\ContainerInterface;

return [
    Server\AuthorizationServer::class => function (ContainerInterface $container) {
        $config = $container->get('config')['oauth'];

        $clientRepository = $container->get(Server\Repositories\ClientRepositoryInterface::class);
        $scopeRepository = $container->get(Server\Repositories\ScopeRepositoryInterface::class);
        $accessTokenRepository = $container->get(Server\Repositories\AccessTokenRepositoryInterface::class);
        $refreshTokenRepository = $container->get(Server\Repositories\RefreshTokenRepositoryInterface::class);
        $userRepository = $container->get(Server\Repositories\UserRepositoryInterface::class);

        $server = new Server\AuthorizationServer(
            $clientRepository,
            $accessTokenRepository,
            $scopeRepository,
            new Server\CryptKey($config['private_key_path'], null, false),
            $config['encryption_key']
        );

        $grant = new Server\Grant\PasswordGrant($userRepository, $refreshTokenRepository);
        $grant->setRefreshTokenTTL(new \DateInterval('P1M'));
        $server->enableGrantType($grant, new \DateInterval('PT1H'));

        $grant = new Server\Grant\RefreshTokenGrant($refreshTokenRepository);
        $grant->setRefreshTokenTTL(new \DateInterval('P1M'));
        $server->enableGrantType($grant, new \DateInterval('PT1H'));

        return $server;
    },
    Server\ResourceServer::class => function (ContainerInterface $container) {
        $config = $container->get('config')['oauth'];

        $accessTokenRepository = $container->get(Server\Repositories\AccessTokenRepositoryInterface::class);

        return new Server\ResourceServer(
            $accessTokenRepository,
            new Server\CryptKey($config['public_key_path'], null, false)
        );
    },
    Server\Repositories\ClientRepositoryInterface::class => function (ContainerInterface $container) {
        $config = $container->get('config')['oauth'];
        return new Infrastructure\Entity\ClientRepository($config['clients']);
    },
    Api\Infrastructure\Framework\Middleware\ResourceServerMiddleware::class => \DI\autowire(Api\Infrastructure\Framework\Middleware\ResourceServerMiddleware::class),
    Server\Repositories\ScopeRepositoryInterface::class => \DI\autowire(Infrastructure\Entity\ScopeRepository::class),
    Server\Repositories\AccessTokenRepositoryInterface::class => \DI\autowire(Infrastructure\Entity\AccessTokenRepository::class),
    Server\Repositories\RefreshTokenRepositoryInterface::class => \DI\autowire(Infrastructure\Entity\RefreshTokenRepository::class),
    Server\Repositories\UserRepositoryInterface::class => \DI\autowire(Infrastructure\Entity\UserRepository::class),

    'config' => [
        'oauth' => [
            'public_key_path' => APP_PATH . getenv('API_OAUTH_PUBLIC_KEY_PATH'),
            'private_key_path' => APP_PATH . getenv('API_OAUTH_PRIVATE_KEY_PATH'),
            'encryption_key' => getenv('API_OAUTH_ENCRYPTION_KEY'),
            'clients' => [
                'app' => [
                    'secret' => null,
                    'name' => 'App',
                    'redirect_uri' => null,
                    'is_confidential' => false,
                ],
            ],
        ],
    ],
];
