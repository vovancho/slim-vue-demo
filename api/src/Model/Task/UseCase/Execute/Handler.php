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

    public function handle(Command $command): void
    {
        $errorCase = rand(1, 100) > 100;

        try {
            $task = $this->getTask($command->id);
            if ($task->isWait()) {
                $task->execute();
                $this->flusher->flush($task);

                while ($task->isExecuting()) {
                    sleep(rand(1, 5));

                    $task = $this->getTask($command->id, true);
                    if ($task->isExecute()) {
                        $this->tryError($task, $errorCase);

                        $task->addPercent(rand(10, 25));
                        if ($task->getProcessPercent() === 100) {
                            $task->complete();
                            $this->removePosition($task);
                        }
                        $this->flusher->flush($task);
                    }
                }
            }
        } catch (\Exception $e) {
            if (isset($task)) {
                $task->error($e);
                $this->removePosition($task);
            } else {
                throw $e;
            }
        }
    }

    private function getTask($id, $force = false): Task
    {
        $task = $this->tasks->get($id, $force);
        $position = $this->tasks->position($task->getId());
        $task->setPosition($position);

        return $task;
    }

    private function tryError(Task $task, $errorCase = false): void
    {
        $percent = $task->getProcessPercent();
        if ($errorCase && $percent > 50) {
            throw new \DomainException("Ошибка на $percent процентах");
        }
    }

    private function removePosition(Task $task): void
    {
        $positionEntity = $this->tasks->getPosition($task);
        $this->tasks->resetPosition($positionEntity);
    }
}
