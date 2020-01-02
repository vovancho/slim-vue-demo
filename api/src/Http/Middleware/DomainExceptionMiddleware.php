<?php

declare(strict_types=1);

namespace Api\Http\Middleware;

use Api\Http\JsonResponse;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;


class DomainExceptionMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (\DomainException $e) {
            return JsonResponse::create([
                'error' => $e->getMessage(),
            ], StatusCodeInterface::STATUS_BAD_REQUEST);
        }
    }
}
