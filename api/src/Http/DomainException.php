<?php

declare(strict_types=1);

namespace Api\Http;


use Fig\Http\Message\StatusCodeInterface;
use Throwable;

class DomainException extends \DomainException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $code = $code ?: StatusCodeInterface::STATUS_BAD_REQUEST;
        parent::__construct($message, $code, $previous);
    }
}
