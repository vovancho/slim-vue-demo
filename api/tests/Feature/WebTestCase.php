<?php

declare(strict_types=1);

namespace Api\Test\Feature;

use Api\Infrastructure\Framework\Handlers\HttpErrorHandler;
use DI\ContainerBuilder;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request as SlimRequest;
use Slim\Psr7\Uri;


class WebTestCase extends TestCase
{
    private $fixtures = [];

    protected function get(string $uri, array $headers = []): ResponseInterface
    {
        $request = $this->method($uri, 'GET', [], $headers);
        return $this->app()->handle($request);
    }

    protected function post(string $uri, array $params = [], array $headers = []): ResponseInterface
    {
        $request = $this->method($uri, 'POST', $params, $headers);
        return $this->app()->handle($request);
    }

    protected function delete(string $uri, array $params = [], array $headers = []): ResponseInterface
    {
        $request = $this->method($uri, 'DELETE', $params, $headers);
        return $this->app()->handle($request);
    }

    protected function method(
        string $path,
        string $method,
        array $bodyParams = [],
        array $headers = [],
        array $serverParams = []
    ): Request
    {
        preg_match('/(.*)\?(.*)|(.*)/', $path, $matches);

        $uri = new Uri('', '', 80, $matches[3] ?? $matches[1], $matches[2] ?? '');
        $handle = fopen('php://temp', 'w+');
        $stream = (new StreamFactory())->createStreamFromResource($handle);

        if ($bodyParams) {
            $stream->write(json_encode($bodyParams));
        }

        $h = new Headers();
        foreach ($headers as $name => $value) {
            $h->addHeader($name, $value);
        }

        return (new SlimRequest($method, $uri, $h, [], $serverParams, $stream))
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Accept', 'application/json');
    }

    protected function loadFixtures(array $fixtures): void
    {
        $container = $this->container();
        $em = $container->get(EntityManagerInterface::class);
        $loader = new Loader();
        foreach ($fixtures as $name => $class) {
            if ($container->has($class)) {
                $fixture = $container->get($class);
            } else {
                $fixture = new $class;
            }
            $loader->addFixture($fixture);
            $this->fixtures[$name] = $fixture;
        }
        $executor = new ORMExecutor($em, new ORMPurger($em));
        $executor->execute($loader->getFixtures());
    }

    protected function getFixture($name)
    {
        if (!array_key_exists($name, $this->fixtures)) {
            throw new \InvalidArgumentException('Undefined fixture ' . $name);
        }
        return $this->fixtures[$name];
    }

    protected function app(): App
    {
        // Build PHP-DI Container instance
        $container = $this->container();

        // Instantiate the app
        AppFactory::setContainer($container);
        $app = AppFactory::create();

        // Register middleware
        $middleware = require APP_PATH . 'app/middleware.php';
        $middleware($app);

        $responseFactory = $app->getResponseFactory();
        $callableResolver = $app->getCallableResolver();
        $errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);

        $app->addRoutingMiddleware();

        // Add Error Middleware
        $errorMiddleware = $app->addErrorMiddleware(false, false, false);
        $errorMiddleware->setDefaultErrorHandler($errorHandler);

        // Register routes
        $routes = require APP_PATH . 'app/routes.php';
        $routes($app);
        return $app;
    }

    private function container(): ContainerInterface
    {
        // Instantiate PHP-DI ContainerBuilder
        $containerBuilder = new ContainerBuilder();
        // Container intentionally not compiled for tests.
        // Set up settings
        $containerConfig = require APP_PATH . 'app/container.php';
        $containerConfig($containerBuilder);

        // Build PHP-DI Container instance
        return $containerBuilder->build();
    }
}
