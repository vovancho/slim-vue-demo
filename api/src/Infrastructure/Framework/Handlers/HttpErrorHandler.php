<?php

declare(strict_types=1);

namespace Api\Infrastructure\Framework\Handlers;

use Api\Http\DomainException;
use Api\Http\ValidationException;
use Api\Infrastructure\Framework\Actions\ActionError;
use Api\Infrastructure\Framework\Actions\ActionPayload;
use Api\Model\EntityNotFoundException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Exception;
use Slim\Handlers\ErrorHandler as SlimErrorHandler;
use Slim\Interfaces\CallableResolverInterface;
use Throwable;

class HttpErrorHandler extends SlimErrorHandler
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(CallableResolverInterface $callableResolver, ResponseFactoryInterface $responseFactory, LoggerInterface $logger)
    {
        parent::__construct($callableResolver, $responseFactory);
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    protected function respond(): ResponseInterface
    {
        $exception = $this->exception;
        $error = new ActionError(
            ActionError::SERVER_ERROR,
            'An internal error has occurred while processing your request.'
        );
        $error->setDescription($exception->getMessage());
        $statusCode = $exception->getCode() ?: 500;

        if ($exception instanceof Exception\HttpException) {
            $this->httpExceptionConfig($error, $exception);
        } elseif ($exception instanceof \Exception || $exception instanceof Throwable) {
            $this->logicExceptionConfig($error, $exception);
        }

        $payload = new ActionPayload($statusCode, null, $error);
        $encodedPayload = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $response = $this->responseFactory->createResponse($statusCode);
        $response->getBody()->write($encodedPayload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    protected function logError(string $error): void
    {
        $this->logger->error($error);
    }

    protected function httpExceptionConfig(ActionError $action, Exception\HttpException $exception): void
    {
        switch (get_class($exception)) {
            case Exception\HttpNotFoundException::class:
                $action->setType(ActionError::RESOURCE_NOT_FOUND);
                return;
            case Exception\HttpMethodNotAllowedException::class:
                $action->setType(ActionError::NOT_ALLOWED);
                return;
            case Exception\HttpUnauthorizedException::class:
                $action->setType(ActionError::UNAUTHENTICATED);
                return;
            case Exception\HttpForbiddenException::class:
                $action->setType(ActionError::INSUFFICIENT_PRIVILEGES);
                return;
            case Exception\HttpBadRequestException::class:
                $action->setType(ActionError::BAD_REQUEST);
                return;
            case Exception\HttpNotImplementedException::class:
                $action->setType(ActionError::NOT_IMPLEMENTED);
                return;
        }
    }

    protected function logicExceptionConfig(ActionError $action, \Exception $exception)
    {
        switch (get_class($exception)) {
            case ValidationException::class:
                /** @var ValidationException $exception */
                $action->setType(ActionError::VALIDATION_ERROR);
                $action->setFormErrors($exception->getErrors()->toArray());
                return;
            case EntityNotFoundException::class:
            case DomainException::class:
                $action->setType(ActionError::BAD_REQUEST);
                return;
        }
    }
}
