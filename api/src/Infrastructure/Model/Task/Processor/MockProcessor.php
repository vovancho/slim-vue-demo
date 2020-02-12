<?php

declare(strict_types=1);

namespace Api\Infrastructure\Model\Task\Processor;


use Api\Http\DomainException;
use Generator;

class MockProcessor extends TaskProcessor
{
    private $errorCase;

    function process(): Generator
    {
        $completedPercent = 0;
        $this->errorCase = rand(1, 100) > 100;
        $addedPercent = rand(10, 25);
        $stages = ceil(100 / $addedPercent);

        for ($i = 1; $i <= $stages; $i++) {
            $this->tryError($completedPercent);

            sleep(rand(1, 5));
            $completedPercent += $addedPercent;

            yield $addedPercent;
        }
    }

    private function tryError($percent): void
    {
        if ($this->errorCase && $percent > 50) {
            throw new DomainException("Ошибка на $percent процентах");
        }
    }
}
