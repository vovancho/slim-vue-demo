<?php

declare(strict_types=1);

use App\Http\Middleware;
use App\Http\Validator\ValidationExceptionMiddleware;
use Middlewares\ContentLanguage;
use Slim\App;
use Slim\Middleware\ErrorMiddleware;

return static function (App $app): void {
    $app->add(Middleware\OAuthExceptionMiddleware::class);
    $app->add(Middleware\DomainExceptionMiddleware::class);
    $app->add(ValidationExceptionMiddleware::class);
    $app->add(Middleware\ClearEmptyInputMiddleware::class);
    $app->add(Middleware\TranslatorLocaleMiddleware::class);
    $app->add(ContentLanguage::class);
    $app->addBodyParsingMiddleware();
    $app->addRoutingMiddleware();
    $app->add(ErrorMiddleware::class);
};
