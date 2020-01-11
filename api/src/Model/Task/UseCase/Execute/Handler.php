<?php

declare(strict_types=1);

namespace Api\Model\Task\UseCase\Execute;


use Api\Model\Flusher;
use Api\Model\Task\Entity\Task\Task;
use Api\Model\Task\Entity\Task\TaskRepository;

class Handler
{
    private $tasks;
    private $flusher;

    public function __construct(
        TaskRepository $tasks,
        Flusher $flusher
    )
    {
        $this->tasks = $tasks;
        $this->flusher = $flusher;
    }

    public function handle(Command $command)
    {
        $errorCase = rand(1, 100) > 100;

        try {
            /** @var Task $task */
            $task = $this->tasks->get($command->id);
            $position = $this->tasks->position($task->getId());
            $task->setPosition($position);

            if ($task->isWait()) {
                $task->execute();
                $this->flusher->flush($task);
            }

            while ($task->isExecuting()) { // TODO $task not updated
                $this->processMock($task, $errorCase);

                try {
                    $task->complete();
                    $positionEntity = $this->tasks->getPosition($task);
                    $this->tasks->resetPosition($positionEntity);
                } catch (\DomainException $e) {
                }
                $this->flusher->flush($task);
                /** @var Task $task */
                $task = $this->tasks->get($command->id);
                $position = $this->tasks->position($task->getId());
                $task->setPosition($position);
            }
        } catch (\Exception $e) {
            if (isset($task)) {
                $task->error($e);
                $positionEntity = $this->tasks->getPosition($task);
                $this->tasks->resetPosition($positionEntity);
                $this->flusher->flush($task);
            } else {
                throw $e;
            }
        }
    }

    protected function processMock(Task $task, $errorCase = false)
    {
        sleep(rand(1, 5));
        $task->addPercent(rand(10, 25));
        $percent = $task->getProcessPercent();

        if ($errorCase && $percent > 50) {
            throw new \DomainException("Ошибка на $percent процентах");
        }
    }
}
