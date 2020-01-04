<?php
declare(strict_types=1);

use Api\Infrastructure\Framework\Middleware\ResourceServerMiddleware;
use Slim\App;
use Api\Http\Action;
use Api\Http\Middleware;

return function (App $app) {
    $container = $app->getContainer();

    $app->addBodyParsingMiddleware();
    $app->add(Middleware\DomainExceptionMiddleware::class);
    $app->add(Middleware\ValidationExceptionMiddleware::class);
    $app->add(Middleware\TranslateOAuthExceptionMiddleware::class);

    $auth = $container->get(ResourceServerMiddleware::class);

    $app->get('/', Action\HomeAction::class . ':handle');

    $app->post('/auth/signup', Action\Auth\SignUp\RequestAction::class . ':handle');
    $app->post('/auth/signup/confirm', Action\Auth\SignUp\ConfirmAction::class . ':handle');

    $app->post('/oauth/auth', Action\Auth\OAuthAction::class . ':handle');
};
