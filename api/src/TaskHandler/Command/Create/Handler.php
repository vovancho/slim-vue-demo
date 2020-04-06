<?php

declare(strict_types=1);

namespace App\TaskHandler\Command\Create;

use App\Auth\Entity\User\Id as UserId;
use App\Auth\Entity\User\UserRepository;
use App\TaskHandler\Entity\Author\Author;
use App\TaskHandler\Entity\Author\Id as AuthorId;
use App\TaskHandler\Entity\Author\Email;
use App\TaskHandler\Entity\Task\Position;
use App\TaskHandler\Entity\Task\Task;
use App\TaskHandler\Entity\Task\TaskRepository;
use App\TaskHandler\Entity\Task\Visibility;
use App\TaskHandler\Entity\Task\Id as TaskId;
use DateTimeImmutable;
use App\Flusher;

class Handler
{
    private TaskRepository $tasks;
    private UserRepository $users;
    private Flusher $flusher;

    public function __construct(TaskRepository $tasks, UserRepository $users, Flusher $flusher)
    {
        $this->tasks = $tasks;
        $this->users = $users;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): Task
    {
        $user = $this->users->get(new UserId($command->author));

        $task = new Task(
            TaskId::generate(),
            new DateTimeImmutable(),
            new Author(new AuthorId($user->getId()->getValue()), new Email($user->getEmail()->getValue())),
            new Visibility($command->visibility),
            $command->name
        );

        $position = new Position($task);

        $this->tasks->add($task, $position);
        $this->flusher->flush($task);

        return $task;
    }
}
