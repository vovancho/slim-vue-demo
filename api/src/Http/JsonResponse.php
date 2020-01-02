<?php
/**
 * Created by PhpStorm.
 * User: Владимир
 * Date: 30.12.2019
 * Time: 8:34
 */
declare(strict_types=1);

namespace Api\Http;


use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Factory\ResponseFactory;

class JsonResponse
{
    public static function create($data, $status = StatusCodeInterface::STATUS_OK): ResponseInterface
    {
        $response = (new ResponseFactory())->createResponse($status);
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
