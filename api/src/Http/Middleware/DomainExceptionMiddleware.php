<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use DomainException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class DomainExceptionMiddleware implements MiddlewareInterface
{
    private LoggerInterface $logger;
    private ResponseFactoryInterface $factory;

    public function __construct(LoggerInterface $logger, ResponseFactoryInterface $factory)
    {
        $this->logger = $logger;
        $this->factory = $factory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (DomainException $exception) {
            $this->logger->warning($exception->getMessage(), [
                'exception' => $exception,
                'url' => (string)$request->getUri(),
            ]);

            return $this->jsonResponse([
                'message' => $exception->getMessage(),
            ], $exception->getCode() ?: 409);
        }
    }

    private function jsonResponse($data, int $status = 200): ResponseInterface
    {
        $response = $this->factory
            ->createResponse($status)
            ->withHeader('Content-Type', 'application/json');

        $response->getBody()->write(json_encode($data, JSON_THROW_ON_ERROR, 512));

        return $response;
    }
}
