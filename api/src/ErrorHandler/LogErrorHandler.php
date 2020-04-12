<?php

declare(strict_types=1);

namespace App\ErrorHandler;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;
use Slim\Handlers\ErrorHandler;
use Slim\Interfaces\CallableResolverInterface;

class LogErrorHandler extends ErrorHandler
{
    private LoggerInterface $logger;

    public function __construct(
        CallableResolverInterface $callableResolver,
        ResponseFactoryInterface $responseFactory,
        LoggerInterface $logger
    ) {
        parent::__construct($callableResolver, $responseFactory);
        $this->logger = $logger;
    }

    protected function writeToErrorLog(): void
    {
        $this->logger->error($this->exception->getMessage(), [
            'namespace' => get_class($this->exception),
            'file' => "{$this->exception->getFile()}:{$this->exception->getLine()}",
            'url' => (string)$this->request->getUri(),
            'trace' => $this->exception->getTraceAsString(),
        ]);
    }
}
