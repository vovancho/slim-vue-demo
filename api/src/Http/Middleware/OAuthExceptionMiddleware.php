<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use DomainException;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class OAuthExceptionMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $response = $handler->handle($request);
        } catch (OAuthServerException $e) {
            throw new DomainException($this->translateException($e), $e->getHttpStatusCode());
        }

        return $response;
    }

    private function translateException(OAuthServerException $exception)
    {
        switch ($exception->getCode()) {
            case 10:
                return 'Неверный E-Mail или Пароль.';
            default:
                return $exception->getMessage();
        }
    }
}
