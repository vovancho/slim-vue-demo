<?php

declare(strict_types=1);

use App\Http\Action;
use App\Http\Middleware\ResourceServerMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return static function (App $app) {
    $app->group('/v1', function (RouteCollectorProxy $group) use ($app): void {
        $container = $app->getContainer();
        $auth = $container->get(ResourceServerMiddleware::class);

        $group->get('/', Action\V1\HomeAction::class);

        $group->group('/auth', function (RouteCollectorProxy $group): void {
            $group->post('/signup', Action\V1\Auth\SignUp\RequestAction::class);
            $group->post('/signup/confirm', Action\V1\Auth\SignUp\ConfirmAction::class);
        });

        $group->post('/oauth/auth', Action\V1\Auth\OAuthAction::class);

        $group->group('/tasks', function (RouteCollectorProxy $group) {
            $group->get('', Action\V1\Task\IndexAction::class);
            $group->post('/create', Action\V1\Task\CreateAction::class);
            $group->delete('/{id}/cancel', Action\V1\Task\CancelAction::class);
        })->add($auth);
    });
};
