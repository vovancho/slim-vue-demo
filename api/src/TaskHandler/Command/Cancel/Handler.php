<?php

declare(strict_types=1);

namespace App\TaskHandler\Command\Cancel;

use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\User;
use App\Auth\Entity\User\UserRepository;
use App\TaskHandler\Entity\Task\Task;
use App\TaskHandler\Entity\Task\TaskRepository;
use App\Flusher;
use DomainException;

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

    public function handle(Command $command)
    {
        /** @var User $user */
        $user = $this->users->get(new Id($command->user));
        /** @var Task $task */
        $task = $this->tasks->get(new \App\TaskHandler\Entity\Task\Id($command->id));

        if ($task->getAuthor()->getId()->getValue() !== $user->getId()->getValue()) {
            throw new DomainException('Задача не принадлежит пользователю.');
        }

        $task->cancel();
        if ($task->isWait()) {
            $this->removePosition($task);
        }

        $this->flusher->flush($task);
    }

    private function removePosition(Task $task): void
    {
        $positionEntity = $this->tasks->getPosition($task);
        $this->tasks->resetPosition($positionEntity);
    }
}
