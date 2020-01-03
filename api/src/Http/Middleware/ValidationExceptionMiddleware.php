<?php

declare(strict_types=1);

namespace Api\Http\Middleware;

use Api\Http\JsonResponse;
use Api\Http\ValidationException;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;


class ValidationExceptionMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (ValidationException $e) {
            return new JsonResponse([
                'errors' => $e->getErrors()->toArray(),
            ], StatusCodeInterface::STATUS_BAD_REQUEST);
        }
    }
}
