<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Task;

use App\Http\JsonResponse;
use App\Http\Validator\Validator;
use App\TaskHandler\Command\Create\Command;
use App\TaskHandler\Command\Create\Handler;
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
 *             @OA\Property(property="id", type="string", description="ID задачи",
 *                          example="3b525ad0-489f-11ea-a264-0242ac140008"),
 *             @OA\Property(property="pushed_at", type="string", description="Дата добавления",
 *                          example="2020-02-06 05:12:12")
 *         ),
 *     ),
 *     @OA\Response(response=401, ref="#/components/responses/401"),
 *     @OA\Response(response=400, ref="#/components/responses/ValidationError"),
 *     @OA\Response(response=405, ref="#/components/responses/405")
 * )
 */
class CreateAction implements RequestHandlerInterface
{
    private Handler $handler;
    private Validator $validator;

    public function __construct(Handler $handler, Validator $validator)
    {
        $this->handler = $handler;
        $this->validator = $validator;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();

        $command = new Command();
        $command->author = $request->getAttribute('oauth_user_id');
        $command->visibility = $data['visibility'] ?? '';
        $command->name = $data['name'] ?? '';

        $this->validator->validate($command);

        $task = $this->handler->handle($command);

        return new JsonResponse([
            'id' => $task->getId()->getValue(),
            'pushed_at' => $task->getPushedAt()->format('Y-m-d H:i:s'),
        ], 201);
    }
}

/**
 * @OA\Schema(
 *      schema="TaskCreate",
 *      required={"name", "visibility"},
 *      @OA\Property(property="name", type="string"),
 *      @OA\Property(property="visibility", type="string"),
 *      example={"name":"Новая задача", "visibility":"public"}
 * )
 */
