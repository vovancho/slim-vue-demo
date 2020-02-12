<?php
/**
 * Created by PhpStorm.
 * User: Владимир
 * Date: 02.01.2020
 * Time: 14:00
 */

namespace Api\Infrastructure\Framework\Middleware;


use League\OAuth2\Server\ResourceServer;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Response as SlimResponse;
use Exception;
use League\OAuth2\Server\Exception\OAuthServerException;


class ResourceServerMiddleware
{
    /**
     * @var ResourceServer
     */
    private $server;

    /**
     * @param ResourceServer $server
     */
    public function __construct(ResourceServer $server)
    {
        $this->server = $server;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $request = $this->server->validateAuthenticatedRequest($request);
        return $handler->handle($request);
    }
}
