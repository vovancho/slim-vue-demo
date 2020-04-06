<?php

declare(strict_types=1);

use App\Auth\Entity\OAuth;
use App\Auth\Service\Tokenizer;
use App\Http\Middleware\ResourceServerMiddleware;
use League\OAuth2\Server;
use Psr\Container\ContainerInterface;

return [
    Tokenizer::class => function (ContainerInterface $container): Tokenizer {
        $config = $container->get('config')['auth'];

        return new Tokenizer(new DateInterval($config['token_ttl']));
    },
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
        return new OAuth\ClientRepository($config['clients']);
    },
    ResourceServerMiddleware::class => \DI\autowire(ResourceServerMiddleware::class),
    Server\Repositories\ScopeRepositoryInterface::class => \DI\autowire(OAuth\ScopeRepository::class),
    Server\Repositories\AccessTokenRepositoryInterface::class => \DI\autowire(OAuth\AccessTokenRepository::class),
    Server\Repositories\RefreshTokenRepositoryInterface::class => \DI\autowire(OAuth\RefreshTokenRepository::class),
    Server\Repositories\UserRepositoryInterface::class => \DI\autowire(OAuth\OAuthRepository::class),

    'config' => [
        'auth' => [
            'token_ttl' => 'PT5M',
        ],
        'oauth' => [
            'public_key_path' => __DIR__ . '/../../' . getenv('API_OAUTH_PUBLIC_KEY_PATH'),
            'private_key_path' => __DIR__ . '/../../' . getenv('API_OAUTH_PRIVATE_KEY_PATH'),
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
