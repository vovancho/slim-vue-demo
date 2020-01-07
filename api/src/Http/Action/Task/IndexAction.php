<?php

declare(strict_types=1);

namespace Api\Http\Action\Task;


use Api\Http\JsonResponse;
use Api\Infrastructure\ReadModel\Pagination;
use Api\ReadModel\Task\TaskReadRepository;
use Api\ReadModel\User\UserReadRepository;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class IndexAction implements RequestHandlerInterface
{
    private $tasks;
    private $users;

    public function __construct(TaskReadRepository $tasks, UserReadRepository $users)
    {
        $this->tasks = $tasks;
        $this->users = $users;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (!$user = $this->users->find($request->getAttribute('oauth_user_id'))) {
            return new JsonResponse([], 403);
        }
        $pagination = Pagination::createByRequest($request);

        $total = $this->tasks->countByUser($user->getId()->getId());
        $tasks = $this->tasks->allByUser($user->getId()->getId(), $pagination);

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
            'user_id' => $task['user_id'],
            'user_email' => $task['email'],
            'type' => $task['type'],
            'name' => $task['name'],
            'status' => $task['status'],
            'process_percent' => $task['process_percent'],
            'error_message' => $task['error_message'],
            'position' => $task['position'],
        ];
    }
}
