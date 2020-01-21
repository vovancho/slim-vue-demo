<?php

declare(strict_types=1);

namespace Api\Model\Task\UseCase\Execute;


use Api\Infrastructure\Model\Task\Processor\TaskProcessor;

class Handler
{
    private $processor;

    public function __construct(TaskProcessor $processor)
    {
        $this->processor = $processor;
    }

    public function handle(Command $command): void
    {
        $this->processor->run($command->id);
    }
}
