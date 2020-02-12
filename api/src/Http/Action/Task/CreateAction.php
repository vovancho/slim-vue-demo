<?php

declare(strict_types=1);

namespace Api\Http\Action\Task;


use Api\Http\JsonResponse;
use Api\Http\ValidationException;
use Api\Http\Validator\Validator;
use Api\Model\Task\UseCase\Create\Command;
use Api\Model\Task\UseCase\Create\Handler;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use OpenApi\Annotations as OA;

/**
 * @OA\Post(
 *     path="/tasks/create",
 *     summary="Добавить новую задачу",
 *     tags={"Обработка задач"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/TaskCreate")
 *     ),
 *     @OA\Response(response=201, description="Задача добавлена",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="string", description="ID задачи", example="3b525ad0-489f-11ea-a264-0242ac140008"),
 *             @OA\Property(property="pushed_at", type="string", description="Дата добавления", example="2020-02-06 05:12:12")
 *         ),
 *     ),
 *     @OA\Response(response=401, ref="#/components/responses/401"),
 *     @OA\Response(response=400, ref="#/components/responses/ValidationError"),
 *     @OA\Response(response=405, ref="#/components/responses/405")
 * )
 */
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

/**
 * @OA\Schema(
 *      schema="TaskCreate",
 *      required={"name", "type"},
 *      @OA\Property(property="name", type="string"),
 *      @OA\Property(property="type", type="string"),
 *      example={"name":"Новая задача", "type":"public"}
 * )
 */
