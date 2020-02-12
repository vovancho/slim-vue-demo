<?php

declare(strict_types=1);

namespace Api\Http\Action\Auth\SignUp;

use Api\Http\JsonResponse;
use Api\Http\ValidationException;
use Api\Http\Validator\Validator;
use Api\Model\User\UseCase\SignUp\Request\Command;
use Api\Model\User\UseCase\SignUp\Request\Handler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use OpenApi\Annotations as OA;

/**
 * @OA\Post(
 *     path="/auth/signup",
 *     summary="Зарегистрировать пользователя",
 *     tags={"Авторизация"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/UserCreate")
 *     ),
 *     @OA\Response(response=201, description="E-Mail нового пользователя",
 *         @OA\JsonContent(
 *             @OA\Property(property="email", type="string", example="new-user@mail.ru")
 *         )
 *     ),
 *     @OA\Response(response=400, ref="#/components/responses/ValidationError"),
 *     @OA\Response(response=405, ref="#/components/responses/405")
 * )
 */
class RequestAction implements RequestHandlerInterface
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

        return new JsonResponse([
            'email' => $command->email,
        ], 201);
    }

    private function deserialize(ServerRequestInterface $request): Command
    {
        $body = $request->getParsedBody();

        $command = new Command();

        $command->email = $body['email'] ?? '';
        $command->password = $body['password'] ?? '';

        return $command;
    }
}

/**
 * @OA\Schema(
 *      schema="UserCreate",
 *      required={"email", "password"},
 *      @OA\Property(property="email", type="string"),
 *      @OA\Property(property="password", type="string", minLength=6),
 *      example={"email":"new-user@mail.ru", "password":"secret"}
 * )
 */
