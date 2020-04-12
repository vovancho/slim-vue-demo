<?php

declare(strict_types=1);

namespace App\TaskHandler\Service\Processor;

use App\Flusher;
use App\TaskHandler\Entity\Task\Id;
use App\TaskHandler\Entity\Task\Task;
use App\TaskHandler\Entity\Task\TaskRepository;
use Exception;
use Generator;
use Psr\Log\LoggerInterface;

abstract class TaskProcessor
{
    private TaskRepository $tasks;
    private Flusher $flusher;
    private LoggerInterface $logger;

    public function __construct(TaskRepository $tasks, Flusher $flusher, LoggerInterface $logger)
    {
        $this->tasks = $tasks;
        $this->flusher = $flusher;
        $this->logger = $logger;
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
                $this->logger->warning($e->getMessage(), [
                    'namespace' => get_class($e),
                    'file' => "{$e->getFile()}:{$e->getLine()}",
                    'trace' => $e->getTraceAsString(),
                ]);

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
