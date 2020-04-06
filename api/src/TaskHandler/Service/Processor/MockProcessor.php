<?php

declare(strict_types=1);

namespace App\TaskHandler\Service\Processor;

use DomainException;
use Generator;

class MockProcessor extends TaskProcessor
{
    public float $probabilityOfError = 0.5; // Вероятность выполнения с ошибкой 50%
    private bool $errorCase;

    public function process(): Generator
    {
        $completedPercent = 0;
        $this->errorCase = rand(1, 100) < ($this->probabilityOfError * 100);
        $addedPercent = rand(25, 40);
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
