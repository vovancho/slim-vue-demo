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
use Slim\Psr7\Headers;
use Slim\Psr7\Interfaces\HeadersInterface;
use Slim\Psr7\Response;

class JsonResponse extends Response
{
    public function __construct($data, int $status = StatusCodeInterface::STATUS_OK, ?HeadersInterface $headers = null)
    {
        $headers = $headers ? $headers : new Headers();
        $headers->addHeader('Content-Type', 'application/json');

        parent::__construct($status, $headers, null);

        $this->getBody()->write(json_encode($data, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE));
        $this->getBody()->rewind();
    }
}
