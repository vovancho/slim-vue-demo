<?php

declare(strict_types=1);

namespace Api\Model\Task\UseCase\Create;


use Api\Model\Base\Uuid1;
use Api\Model\Flusher;
use Api\Model\Task\Entity\Task\Position;
use Api\Model\Task\Entity\Task\Task;
use Api\Model\Task\Entity\Task\TaskRepository;
use Api\Model\User\Entity\User\UserId;
use Api\Model\User\Entity\User\UserRepository;

class Handler
{
    private $tasks;
    private $users;
    private $flusher;

    public function __construct(
        TaskRepository $tasks,
        UserRepository $users,
        Flusher $flusher
    )
    {
        $this->tasks = $tasks;
        $this->users = $users;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): Task
    {
        $user = $this->users->get(new UserId($command->user));

        $task = new Task(
            $id = Uuid1::next(),
            new \DateTimeImmutable(),
            $user,
            $command->type,
            $command->name
        );

        $position = new Position($task);

        $this->tasks->add($task, $position);
        $this->flusher->flush($task);

        return $task;
    }
}
