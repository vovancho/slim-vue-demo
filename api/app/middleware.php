<?php
declare(strict_types=1);

use Api\Http\Middleware;
use Slim\App;

return function (App $app) {
    $app->addBodyParsingMiddleware();
    $app->add(Middleware\OAuthExceptionMiddleware::class);
};
