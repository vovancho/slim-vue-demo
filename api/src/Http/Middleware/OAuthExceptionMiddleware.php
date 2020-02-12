<?php
/**
 * Created by PhpStorm.
 * User: Владимир
 * Date: 04.01.2020
 * Time: 9:54
 */

namespace Api\Http\Middleware;


use Api\Http\DomainException;
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
            throw new DomainException($this->translateException($e), $e->getHttpStatusCode(), $e);
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
