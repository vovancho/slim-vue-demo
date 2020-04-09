<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Task;

use App\Auth\Entity\User\UserReadRepository;
use App\Framework\Pagination;
use App\Http\EmptyResponse;
use App\Http\JsonResponse;
use App\TaskHandler\Entity\Task\TaskReadRepository;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/tasks",
 *     summary="Получить задачи",
 *     tags={"Обработка задач"},
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Страница пагинации",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="itemsPerPage",
 *         in="query",
 *         description="Количество задач на странице",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="sortBy",
 *         in="query",
 *         description="Имя поля сортировки",
 *         @OA\Schema(
 *             type="array",
 *             @OA\Items(@OA\Schema(type="string"))
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="sortDesc",
 *         in="query",
 *         description="Направление сортировки для поля в параметре sortBy",
 *         @OA\Schema(type="boolean")
 *     ),
 *     @OA\Response(response=201, description="Набор задач",
 *         @OA\JsonContent(
 *             @OA\Property(property="total", type="integer", description="Количество задач всего", example="1"),
 *             @OA\Property(property="rows", type="array", description="Массив объектов задач",
 *                          @OA\Items(ref="#/components/schemas/Task")
 *             )
 *         ),
 *     ),
 *     @OA\Response(response=401, ref="#/components/responses/401"),
 *     @OA\Response(response=405, ref="#/components/responses/405")
 * )
 */
class IndexAction implements RequestHandlerInterface
{
    private TaskReadRepository $tasks;
    private UserReadRepository $users;

    public function __construct(TaskReadRepository $tasks, UserReadRepository $users)
    {
        $this->tasks = $tasks;
        $this->users = $users;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (!$user = $this->users->find($request->getAttribute('oauth_user_id'))) {
            return new EmptyResponse(403);
        }
        $pagination = Pagination::createByRequest($request);

        $total = $this->tasks->countByUser($user->getId()->getValue());
        $tasks = $this->tasks->allByUser($user->getId()->getValue(), $pagination);

        return new JsonResponse([
            'total' => $total,
            'rows' => array_map([$this, 'serialize'], $tasks),
        ]);
    }

    private function serialize(array $task): array
    {
        return [
            'id' => $task['id'],
            'pushed_at' => $task['pushed_at'],
            'author_id' => $task['author_id'],
            'author_email' => $task['author_email'],
            'visibility' => $task['visibility'],
            'name' => $task['name'],
            'status' => $task['status'],
            'process_percent' => $task['process_percent'],
            'error_message' => $task['error_message'],
            'error_trace' => $task['error_trace'],
            'position' => $task['position'],
        ];
    }
}

/**
 * @OA\Schema(
 *      schema="Task",
 *      @OA\Property(property="id", type="string"),
 *      @OA\Property(property="pushed_at", type="string"),
 *      @OA\Property(property="author_id", type="string"),
 *      @OA\Property(property="author_email", type="string"),
 *      @OA\Property(property="visibility", type="string", enum={"public", "private"}),
 *      @OA\Property(property="name", type="string"),
 *      @OA\Property(property="status", type="string", enum={"wait", "execute", "complete", "cancel", "error"}),
 *      @OA\Property(property="process_percent", type="integer", minimum=0, maximum=100),
 *      @OA\Property(property="error_message", type="string"),
 *      @OA\Property(property="error_trace", type="string"),
 *      @OA\Property(property="position", type="integer", minimum=1),
 *      example={
 *          "id": "3b525ad0-489f-11ea-a264-0242ac140008",
 *          "pushed_at": "2020-02-06 05:12:12",
 *          "author_id": "a61b0771-a28c-4db7-bbf6-6a219370e0bb",
 *          "author_email": "user@app.dev",
 *          "visibility": "public",
 *          "name": "Новая задача",
 *          "status": "complete",
 *          "process_percent": 100,
 *          "error_message": null,
 *          "error_trace": null,
 *          "position": null
 *     }
 * )
 */
