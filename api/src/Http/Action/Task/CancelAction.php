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
use Slim\Routing\RouteContext;
use OpenApi\Annotations as OA;

/**
 * @OA\Delete(
 *     path="/tasks/cancel/{id}",
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
        $routeContext = RouteContext::fromRequest($request);

        $command = new Command();
        $command->user = $request->getAttribute('oauth_user_id');
        $command->id = $routeContext->getRoute()->getArgument('id');

        return $command;
    }
}
