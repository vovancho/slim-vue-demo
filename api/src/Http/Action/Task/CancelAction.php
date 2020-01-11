<?php

declare(strict_types=1);

namespace Api\Http\Action\Task;


use Api\Http\JsonResponse;
use Api\Http\ValidationException;
use Api\Http\Validator\Validator;
use Api\Model\Task\UseCase\Cancel\Command;
use Api\Model\Task\UseCase\Cancel\Handler;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class CancelAction implements RequestHandlerInterface
{
    private $handler;
    private $validator;

    public function __construct(Handler $handler, Validator $validator)
    {
        $this->handler = $handler;
        $this->validator = $validator;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $command = $this->deserialize($request);

        if ($errors = $this->validator->validate($command)) {
            throw new ValidationException($errors);
        }

        $this->handler->handle($command);

        return new JsonResponse([], 204);
    }

    private function deserialize(ServerRequestInterface $request): Command
    {
        $command = new Command();
        $command->user = $request->getAttribute('oauth_user_id');
        $command->id = $request->getAttribute('route')->getArgument('id');

        return $command;
    }
}
