<?php
/**
 * Created by PhpStorm.
 * User: Владимир
 * Date: 04.01.2020
 * Time: 9:54
 */

namespace Api\Http\Middleware;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\StreamFactory;

class TranslateOAuthExceptionMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        if ($response->getStatusCode() === 400) {
            $json = (string)$response->getBody();

            $content = json_decode($json, true);
            if (!empty($content['error']) && in_array($content['error'], ['invalid_request', 'invalid_grant']) && !empty($content['message'])) {
                $content['error'] = 'Неверный E-Mail или Пароль.';
                $newBody = (new StreamFactory())->createStream(json_encode($content));

                return $response->withBody($newBody);
            }
        }

        return $response;
    }
}
