<?php

declare(strict_types=1);

namespace Api\Http\Action\Auth;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Response;
use OpenApi\Annotations as OA;

/**
 * @OA\Post(
 *     path="/oauth/auth",
 *     summary="Авторизация пользователя/Обновление токена доступа",
 *     tags={"Авторизация"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             oneOf={@OA\Schema(ref="#/components/schemas/OAuthPassword"), @OA\Schema(ref="#/components/schemas/OAuthRefreshToken")},
 *             example={"grant_type":"password", "username":"new-user@mail.ru", "password":"secret", "client_id":"app", "client_secret":"", "access_type":"offline"}
 *         )
 *     ),
 *     @OA\Response(response=200, description="Данные авторизации",
 *         @OA\JsonContent(ref="#/components/schemas/OAuthResponse"),
 *     ),
 *     @OA\Response(response=405, ref="#/components/responses/405"),
 *     @OA\Response(response=400, ref="#/components/responses/AuthInvalidError")
 * )
 */
class OAuthAction implements RequestHandlerInterface
{
    private $server;
    private $logger;

    public function __construct(AuthorizationServer $server, LoggerInterface $logger)
    {
        $this->server = $server;
        $this->logger = $logger;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            return $this->server->respondToAccessTokenRequest($request, new Response());
        } catch (OAuthServerException $exception) {
            $this->logger->warning($exception->getMessage(), ['exception' => $exception]);
            throw $exception;
        }
    }
}

/**
 * @OA\Response(
 *     response="AuthInvalidError",
 *     description="Ошибка авторизации",
 *     @OA\JsonContent(
 *          @OA\Schema(ref="#/components/schemas/ErrorModel"),
 *          example={
 *                      "statusCode": "400",
 *                      "error": {
 *                          "type": "BAD_REQUEST",
 *                          "description": "Неверный E-Mail или Пароль."
 *                      }
 *                  }
 *     )
 * )
 *
 * @OA\Schema(
 *      schema="OAuthPassword",
 *      required={"grant_type", "username", "password", "client_id", "client_secret", "access_type"},
 *      @OA\Property(property="grant_type", type="string", pattern="^password$"),
 *      @OA\Property(property="username", type="string"),
 *      @OA\Property(property="password", type="string", minLength=6),
 *      @OA\Property(property="client_id", type="string", pattern="^app$"),
 *      @OA\Property(property="client_secret", type="string", pattern="^$"),
 *      @OA\Property(property="access_type", type="string", pattern="^offline$"),
 *      example={"grant_type":"password", "username":"new-user@mail.ru", "password":"secret", "client_id":"app", "client_secret":"", "access_type":"offline"}
 * )
 *
 * @OA\Schema(
 *      schema="OAuthRefreshToken",
 *      required={"grant_type", "client_id", "client_secret", "refresh_token"},
 *      @OA\Property(property="grant_type", type="string", pattern="^refresh_token$"),
 *      @OA\Property(property="client_id", type="string", pattern="^app$"),
 *      @OA\Property(property="client_secret", type="string", pattern="^$"),
 *      @OA\Property(property="refresh_token", type="string"),
 *      example={"grant_type":"refresh_token", "client_id":"app", "client_secret":"", "refresh_token":"def50200c237e058820886407037f..."}
 * )
 *
 * @OA\Schema(
 *      schema="OAuthResponse",
 *      @OA\Property(property="token_type", type="string", pattern="^Bearer$"),
 *      @OA\Property(property="expires_in", type="integer"),
 *      @OA\Property(property="access_token", type="string"),
 *      @OA\Property(property="refresh_token", type="string"),
 *      example={"token_type":"Bearer", "expires_in":3600, "access_token":"eyJ0eXAiOiJKV1QiLOiJSUzI1Ni...", "refresh_token":"def50200c237e058820886407037f..."}
 * )
 */
