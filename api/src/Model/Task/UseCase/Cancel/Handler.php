<?php

declare(strict_types=1);

namespace Api\Model\Task\UseCase\Cancel;


use Api\Model\Base\Uuid1;
use Api\Model\Flusher;
use Api\Model\Task\Entity\Task\Task;
use Api\Model\Task\Entity\Task\TaskRepository;
use Api\Model\User\Entity\User\User;
use Api\Model\User\Entity\User\UserId;
use Api\Model\User\Entity\User\UserRepository;
use Zend\EventManager\Exception\DomainException;

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

    public function handle(Command $command)
    {
        /** @var User $user */
        $user = $this->users->get(new UserId($command->user));
        /** @var Task $task */
        $task = $this->tasks->get(new Uuid1($command->id));

        if ($task->getUser()->getId()->getId() !== $user->getId()->getId()) {
            throw new DomainException('Задача не принадлежит пользователю.');
        }

        $task->cancel();
        $this->removePosition($task);

        $this->flusher->flush($task);
    }

    private function removePosition(Task $task): void
    {
        $positionEntity = $this->tasks->getPosition($task);
        $this->tasks->resetPosition($positionEntity);
    }
}
