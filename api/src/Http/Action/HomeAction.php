<?php

declare(strict_types=1);

namespace Api\Http\Action;

use Api\Http\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;


class HomeAction  implements RequestHandlerInterface
{
    public function handle(ServerRequest $request): ResponseInterface
    {
        return JsonResponse::create([
            'name' => 'App API',
            'version' => '1.0',
        ]);
    }
}
