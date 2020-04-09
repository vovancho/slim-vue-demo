<?php

declare(strict_types=1);

namespace App\Test\Functional;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Psr7\Factory\ServerRequestFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request as SlimRequest;
use Slim\Psr7\Uri;

class WebTestCase extends TestCase
{
    private ?App $app = null;
    private ?MailerClient $mailer = null;
    private array $fixtures = [];

    protected function tearDown(): void
    {
        $this->app = null;
        parent::tearDown();
    }

    protected static function json(
        string $method,
        string $path,
        array $body = [],
        array $headers = [],
        array $serverParams = []
    ): Request {
        preg_match('/(.*)\?(.*)|(.*)/', $path, $matches);

        $uri = new Uri('', '', 80, $matches[3] ?? $matches[1], $matches[2] ?? '');
        $handle = fopen('php://temp', 'w+');
        $stream = (new StreamFactory())->createStreamFromResource($handle);

        if ($body) {
            $stream->write(json_encode($body, JSON_THROW_ON_ERROR));
        }

        $h = new Headers();
        foreach ($headers as $name => $value) {
            $h->addHeader($name, $value);
        }

        return (new SlimRequest($method, $uri, $h, [], $serverParams, $stream))
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json');
    }

    protected static function request(string $method, string $path): ServerRequestInterface
    {
        return (new ServerRequestFactory())->createServerRequest($method, $path);
    }

    /**
     * @param array<string|int,string> $fixtures
     */
    protected function loadFixtures(array $fixtures): void
    {
        /** @var ContainerInterface $container */
        $container = $this->app()->getContainer();
        $loader = new Loader();

        foreach ($fixtures as $name => $class) {
            /** @var AbstractFixture $fixture */
            $fixture = $container->get($class);
            $loader->addFixture($fixture);
            $this->fixtures[$name] = $fixture;
        }
        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);
        $executor = new ORMExecutor($em, new ORMPurger($em));
        $executor->execute($loader->getFixtures());
    }

    protected function app(): App
    {
        if ($this->app === null) {
            /** @var App */
            $this->app = (require __DIR__ . '/../../config/app.php')($this->container());
        }
        return $this->app;
    }

    protected function mailer(): MailerClient
    {
        if ($this->mailer === null) {
            $this->mailer = new MailerClient();
        }
        return $this->mailer;
    }

    protected function getFixture($name)
    {
        if (!array_key_exists($name, $this->fixtures)) {
            throw new \InvalidArgumentException('Undefined fixture ' . $name);
        }
        return $this->fixtures[$name];
    }

    private function container(): ContainerInterface
    {
        /** @var ContainerInterface */
        return require __DIR__ . '/../../config/container.php';
    }
}
