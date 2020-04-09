<?php

declare(strict_types=1);

namespace App\TaskHandler\Command\Execute;

use App\TaskHandler\Service\Processor\MockProcessor;

class Handler
{
    private MockProcessor $processor;

    public function __construct(MockProcessor $processor)
    {
        $this->processor = $processor;
    }

    public function handle(Command $command): void
    {
        $this->processor->run($command->id);
    }
}
