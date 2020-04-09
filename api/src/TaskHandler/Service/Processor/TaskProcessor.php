<?php

declare(strict_types=1);

namespace App\TaskHandler\Service\Processor;

use App\Flusher;
use App\TaskHandler\Entity\Task\Id;
use App\TaskHandler\Entity\Task\Task;
use App\TaskHandler\Entity\Task\TaskRepository;
use Exception;
use Generator;

abstract class TaskProcessor
{
    private TaskRepository $tasks;
    private Flusher $flusher;

    public function __construct(TaskRepository $tasks, Flusher $flusher)
    {
        $this->tasks = $tasks;
        $this->flusher = $flusher;
    }

    public function run(Id $taskId): void
    {
        try {
            $task = $this->tasks->get($taskId);
            if ($task->isWait()) {
                $task->execute();
                $this->removePosition($task);
                $this->flusher->flush($task);

                foreach ($this->process() as $addedPercent) {
                    $task = $this->tasks->get($taskId, true);

                    if ($task->isExecute()) {
                        $task->addPercent($addedPercent);
                        if ($task->getProcessPercent() === 100) {
                            $task->complete();
                        }
                        $this->flusher->flush($task);
                    } else {
                        break;
                    }
                }
            }
        } catch (Exception $e) {
            if (isset($task)) {
                if ($task->isWait()) {
                    $this->removePosition($task);
                }
                $task->error($e);
                $this->flusher->flush($task);
            } else {
                throw $e;
            }
        }
    }

    abstract public function process(): Generator;

    private function removePosition(Task $task): void
    {
        $positionEntity = $this->tasks->getPosition($task);
        $this->tasks->resetPosition($positionEntity);
    }
}
