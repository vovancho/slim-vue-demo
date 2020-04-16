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
use Symfony\Contracts\Translation\TranslatorInterface;

class DomainExceptionMiddleware implements MiddlewareInterface
{
    private LoggerInterface $logger;
    private ResponseFactoryInterface $factory;
    private TranslatorInterface $translator;

    public function __construct(
        LoggerInterface $logger,
        ResponseFactoryInterface $factory,
        TranslatorInterface $translator
    ) {
        $this->logger = $logger;
        $this->factory = $factory;
        $this->translator = $translator;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (DomainException $exception) {
            $this->logger->warning($exception->getMessage(), [
                'namespace' => get_class($exception),
                'file' => "{$exception->getFile()}:{$exception->getLine()}",
                'url' => (string)$request->getUri(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return $this->jsonResponse([
                'message' => $this->translator->trans($exception->getMessage(), [], 'exceptions'),
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
