<?php

declare(strict_types=1);

use App\Framework\ResponseEmitter;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Factory\ServerRequestCreatorFactory;

http_response_code(500);

require __DIR__ . '/../vendor/autoload.php';

/** @var ContainerInterface $container */
$container = require __DIR__ . '/../config/container.php';

/** @var App $app */
$app = (require __DIR__ . '/../config/app.php')($container);

$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

$response = $app->handle($request);

$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);
