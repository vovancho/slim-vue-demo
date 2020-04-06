<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Auth\SignUp;

use App\Auth\Command\SignUp\Request\Command;
use App\Auth\Command\SignUp\Request\Handler;
use App\Http\JsonResponse;
use App\Http\Validator\Validator;
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
        $command->password = $data['password'] ?? '';

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonResponse([
            'email' => $command->email,
        ], 201);
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
