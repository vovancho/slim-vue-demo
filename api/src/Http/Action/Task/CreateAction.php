<?php

declare(strict_types=1);

namespace Api\Http\Action\Task;


use Api\Http\JsonResponse;
use Api\Http\ValidationException;
use Api\Http\Validator\Validator;
use Api\Model\Task\UseCase\Task\Command;
use Api\Model\Task\UseCase\Task\Handler;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class CreateAction implements RequestHandlerInterface
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

        $task = $this->handler->handle($command);

        return new JsonResponse([
            'id' => $task->getId()->getId(),
            'pushed_at' => $task->getPushedAt()->format('Y-m-d H:i:s'),
        ], 201);
    }

    private function deserialize(ServerRequestInterface $request): Command
    {
        $body = $request->getParsedBody();

        $command = new Command();

        $command->user = $request->getAttribute('oauth_user_id');
        $command->type = $body['type'] ?? '';
        $command->name = $body['name'] ?? '';

        return $command;
    }
}
