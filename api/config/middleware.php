<?php

declare(strict_types=1);

use App\Http\Middleware;
use App\Http\Validator\ValidationExceptionMiddleware;
use Slim\App;
use Slim\Middleware\ErrorMiddleware;

return static function (App $app): void {
    $app->add(Middleware\OAuthExceptionMiddleware::class);
    $app->add(Middleware\DomainExceptionMiddleware::class);
    $app->add(ValidationExceptionMiddleware::class);
    $app->add(Middleware\ClearEmptyInputMiddleware::class);
    $app->addBodyParsingMiddleware();
    $app->addRoutingMiddleware();
    $app->add(ErrorMiddleware::class);
};
