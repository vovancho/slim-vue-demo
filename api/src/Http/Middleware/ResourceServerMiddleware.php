<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use League\OAuth2\Server\ResourceServer;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;

class ResourceServerMiddleware
{
    /**
     * @var ResourceServer
     */
    private ResourceServer $server;

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
