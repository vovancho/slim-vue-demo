<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Task;

use App\Http\EmptyResponse;
use App\Http\Validator\Validator;
use App\TaskHandler\Command\Cancel\Command;
use App\TaskHandler\Command\Cancel\Handler;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Routing\RouteContext;
use OpenApi\Annotations as OA;

/**
 * @OA\Delete(
 *     path="/tasks/{id}/cancel",
 *     summary="Отменить задачу",
 *     tags={"Обработка задач"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID задачи",
 *         example="3b525ad0-489f-11ea-a264-0242ac140008",
 *         @OA\Schema(type="string", format="uuid")
 *     ),
 *     @OA\Response(response=204, description="Задача отменена"),
 *     @OA\Response(response=401, ref="#/components/responses/401"),
 *     @OA\Response(response=400, ref="#/components/responses/ValidationError"),
 *     @OA\Response(response=405, ref="#/components/responses/405")
 * )
 */
class CancelAction implements RequestHandlerInterface
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
        $routeContext = RouteContext::fromRequest($request);
        $command = new Command();
        $command->user = $request->getAttribute('oauth_user_id');
        $command->id = $routeContext->getRoute()->getArgument('id');

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new EmptyResponse(204);
    }
}
