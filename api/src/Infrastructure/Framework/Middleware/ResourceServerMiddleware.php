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
        try {
            $request = $this->server->validateAuthenticatedRequest($request);
        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse(new SlimResponse());
            // @codeCoverageIgnoreStart
        } catch (Exception $exception) {
            return (new OAuthServerException($exception->getMessage(), 0, 'unknown_error', 500))
                ->generateHttpResponse(new SlimResponse());
            // @codeCoverageIgnoreEnd
        }

        return $handler->handle($request);
    }
}
