<?php

declare(strict_types=1);

namespace Api\Infrastructure\Model\Task\Processor;


use Api\Model\Base\Uuid1;
use Api\Model\Flusher;
use Api\Model\Task\Entity\Task\Task;
use Api\Model\Task\Entity\Task\TaskRepository;
use Generator;

abstract class TaskProcessor
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

    public function run(Uuid1 $taskId): void
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
        } catch (\Exception $e) {
            if (isset($task)) {
                $task->error($e);
                $this->removePosition($task);
                $this->flusher->flush($task);
            } else {
                throw $e;
            }
        }
    }

    abstract function process(): Generator;

    private function removePosition(Task $task): void
    {
        $positionEntity = $this->tasks->getPosition($task);
        $this->tasks->resetPosition($positionEntity);
    }
}
