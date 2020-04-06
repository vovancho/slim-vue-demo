<?php

declare(strict_types=1);

namespace App\Http\Action\V1;

use App\Http\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\Server(
 *         url="http://127.0.0.1:8081/v1/"
 * ),
 *
 * @OA\Info(
 *         version="1.0.0",
 *         title="RESTful API Сервис обработки задач"
 * ),
 *
 * @OA\Get(
 *     path="/",
 *     summary="Версия API",
 *     tags={"Главная"},
 *     @OA\Response(response=401, ref="#/components/responses/401"),
 *     @OA\Response(response=405, ref="#/components/responses/405"),
 *     @OA\Response(response=200, description="Версия API",
 *         @OA\JsonContent(
 *             @OA\Property(property="version", type="string", description="Версия API", example="1.0.0")
 *         ),
 *     )
 * )
 */
class HomeAction implements RequestHandlerInterface
{
    public function handle(ServerRequest $request): ResponseInterface
    {
        return new JsonResponse([
            'name' => 'App API',
            'version' => '1.0',
        ]);
    }
}

/**
 * @OA\Response(
 *     response=401,
 *     description="Ошибка авторизации",
 *     @OA\JsonContent(
 *          @OA\Schema(ref="#/components/schemas/ErrorModel"),
 *          example={
 *                      "message": "The resource owner or authorization server denied the request."
 *                  }
 *     )
 * )
 *
 * @OA\Response(
 *     response=405,
 *     description="Ошибка метода запроса",
 *     @OA\JsonContent(
 *          @OA\Schema(ref="#/components/schemas/ErrorModel"),
 *          example={
*                       "message": "Method not allowed. Must be one of: GET"
 *                  }
 *     )
 * )
 *
 * @OA\Response(
 *     response="HttpError",
 *     description="Ошибка сервера",
 *     @OA\JsonContent(
 *          @OA\Schema(ref="#/components/schemas/ErrorModel"),
 *          example={
 *                      "message": "404 Not Found"
 *                  }
 *     )
 * )
 *
 * @OA\Response(
 *     response="ValidationError",
 *     description="Ошибка валидации",
 *     @OA\JsonContent(
 *          @OA\Schema(ref="#/components/schemas/ValidationErrorModel"),
 *          example={
 *                      "errors": {
 *                          "field1": "Error Message 1.",
 *                          "field2": "Error Message 2."
 *                      }
 *                  }
 *     )
 * )
 *
 * @OA\Schema(
 *      schema="ErrorModel",
 *      @OA\Property(property="message", type="string")
 * )
 *
 * @OA\Schema(
 *      schema="ValidationErrorModel",
 *      anyOf={
 *                @OA\Property(property="message", type="string"),
 *                @OA\Property(property="errors", type="object")
 *            }
 * )
 */
