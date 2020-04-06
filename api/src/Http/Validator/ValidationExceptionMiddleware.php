<?php

declare(strict_types=1);

namespace App\Http\Validator;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationExceptionMiddleware implements MiddlewareInterface
{
    private ResponseFactoryInterface $factory;

    public function __construct(ResponseFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (ValidationException $exception) {
            return $this->jsonResponse([
                'errors' => self::errorsArray($exception->getViolations()),
            ], 422);
        }
    }

    private static function errorsArray(ConstraintViolationListInterface $violations): array
    {
        $errors = [];
        /** @var ConstraintViolationInterface $violation */
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }
        return $errors;
    }

    private function jsonResponse($data, int $status = 200): ResponseInterface
    {
        $response = $this->factory
            ->createResponse($status)
            ->withHeader('Content-Type', 'application/json');

        $response->getBody()->write(json_encode($data, JSON_THROW_ON_ERROR, 512));

        return $response;
    }
}
