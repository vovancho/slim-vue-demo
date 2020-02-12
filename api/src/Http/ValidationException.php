<?php

declare(strict_types=1);

namespace Api\Http;

use Api\Http\Validator\Errors;
use Fig\Http\Message\StatusCodeInterface;

class ValidationException extends \LogicException
{
    private $errors;

    public function __construct(Errors $errors)
    {
        parent::__construct('', StatusCodeInterface::STATUS_BAD_REQUEST);
        $this->errors = $errors;
    }

    public function getErrors(): Errors
    {
        return $this->errors;
    }
}
