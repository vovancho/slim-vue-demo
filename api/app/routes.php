<?php
declare(strict_types=1);

use Api\Infrastructure\Framework\Middleware\ResourceServerMiddleware;
use Slim\App;
use Api\Http\Action;

return function (App $app) {
    $container = $app->getContainer();
    $auth = $container->get(ResourceServerMiddleware::class);

    $app->get('/', Action\HomeAction::class . ':handle')->add($auth);

    $app->post('/auth/signup', Action\Auth\SignUp\RequestAction::class . ':handle');
    $app->post('/auth/signup/confirm', Action\Auth\SignUp\ConfirmAction::class . ':handle');

    $app->post('/oauth/auth', Action\Auth\OAuthAction::class . ':handle');
};
