<?php

declare(strict_types=1);

namespace Api\Http\Action\Auth\SignUp;

use Api\Http\ValidationException;
use Api\Http\Validator\Validator;
use Api\Model\User\UseCase\SignUp\Confirm\Command;
use Api\Model\User\UseCase\SignUp\Confirm\Handler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Api\Http\JsonResponse;
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

        return new JsonResponse([]);
    }

    private function deserialize(ServerRequestInterface $request): Command
    {
        $body = $request->getParsedBody();

        $command = new Command();

        $command->email = $body['email'] ?? '';
        $command->token = $body['token'] ?? '';

        return $command;
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
