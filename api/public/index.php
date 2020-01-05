<?php
declare(strict_types=1);

use Api\Infrastructure\Framework\ResponseEmitter;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;
use Symfony\Component\Dotenv\Dotenv;

define('APP_PATH', realpath('..') . DIRECTORY_SEPARATOR);

require APP_PATH . 'vendor/autoload.php';

if (file_exists(APP_PATH . '.env')) {
    (new Dotenv(true))->load(APP_PATH . '.env');
}

(function () {
// Instantiate PHP-DI ContainerBuilder
    $containerBuilder = new ContainerBuilder();

    if (false) { // Should be set to true in production
        $containerBuilder->enableCompilation(APP_PATH . 'var/cache');
    }

// Set up settings
    $containerConfig = require APP_PATH . 'app/container.php';
    $containerConfig($containerBuilder);

// Build PHP-DI Container instance
    $container = $containerBuilder->build();

// Instantiate the app
    AppFactory::setContainer($container);
    $app = AppFactory::create();

// Register middleware
    $middleware = require APP_PATH . 'app/middleware.php';
    $middleware($app);

// Register routes
    $routes = require APP_PATH . 'app/routes.php';
    $routes($app);

    /** @var bool $displayErrorDetails */
    $displayErrorDetails = $container->get('settings')['displayErrorDetails'];

// Create Request object from globals
    $serverRequestCreator = ServerRequestCreatorFactory::create();
    $request = $serverRequestCreator->createServerRequestFromGlobals();

// Add Routing Middleware
    $app->addRoutingMiddleware();

// Add Error Middleware
    $app->addErrorMiddleware($displayErrorDetails, true, true);

// Run App & Emit Response
    $response = $app->handle($request);
    $responseEmitter = new ResponseEmitter();
    $responseEmitter->emit($response);
})();
