<?php

declare(strict_types=1);

namespace Api\Http\Action;

use Api\Http\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;
use OpenApi\Annotations as OA;

/**
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
 *                      "error":"access_denied",
 *                      "error_description":"The resource owner or authorization server denied the request.",
 *                      "hint":"Missing ""Authorization"" header",
 *                      "message":"The resource owner or authorization server denied the request."
 *                  }
 *     )
 * )
 *
 * @OA\Response(
 *     response="HttpError",
 *     description="Ошибка сервера",
 *     @OA\JsonContent(
 *          @OA\Schema(ref="#/components/schemas/HttpErrorModel"),
 *          example={
 *                      "statusCode":404,
 *                      "error":{
 *                          "type": "RESOURCE_NOT_FOUND",
 *                          "description": "Not found."
 *                      }
 *                  }
 *     )
 * )
 *
 * @OA\Schema(
 *      schema="ErrorModel",
 *      @OA\Property(property="error", type="string"),
 *      @OA\Property(property="error_description", type="string"),
 *      @OA\Property(property="hint", type="string"),
 *      @OA\Property(property="message", type="string")
 * )
 *
 * @OA\Schema(
 *      schema="HttpErrorModel",
 *      @OA\Property(property="statusCode", type="integer"),
 *      @OA\Property(property="error", type="object",
 *          @OA\Property(property="type", type="string"),
 *          @OA\Property(property="description", type="string")
 *      )
 * )
 */
