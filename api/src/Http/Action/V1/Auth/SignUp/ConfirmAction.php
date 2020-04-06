<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Auth\SignUp;

use App\Auth\Command\SignUp\Confirm\Command;
use App\Auth\Command\SignUp\Confirm\Handler;
use App\Http\EmptyResponse;
use App\Http\Validator\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use OpenApi\Annotations as OA;

/**
 * @OA\Post(
 *     path="/auth/signup/confirm",
 *     summary="Подтвердить зарегистрированного пользователя",
 *     tags={"Авторизация"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/UserCreateConfirm")
 *     ),
 *     @OA\Response(response=200, description="E-Mail пользователя подтвержден"),
 *     @OA\Response(response=400, ref="#/components/responses/ValidationError"),
 *     @OA\Response(response=405, ref="#/components/responses/405")
 * )
 */
class ConfirmAction implements RequestHandlerInterface
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

        $command->email = $data['email'] ?? '';
        $command->token = $data['token'] ?? '';

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new EmptyResponse(200);
    }
}

/**
 * @OA\Schema(
 *      schema="UserCreateConfirm",
 *      required={"email", "token"},
 *      @OA\Property(property="email", type="string"),
 *      @OA\Property(property="token", type="string", minLength=6, maxLength=6),
 *      example={"email":"new-user@mail.ru", "token":"123456"}
 * )
 */
