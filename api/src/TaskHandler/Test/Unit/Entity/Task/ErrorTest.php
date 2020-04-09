<?php

declare(strict_types=1);

namespace App\TaskHandler\Test\Unit\Entity\Task;

use App\TaskHandler\Entity\Task\Error;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\TaskHandler\Entity\Task\Error
 */
class ErrorTest extends TestCase
{
    public function testSuccess(): void
    {
        $error = new Error($message = 'error', $trace = 'trace');

        self::assertEquals($message, $error->getMessage());
        self::assertEquals($trace, $error->getTrace());
    }

    /**
     * @dataProvider getCases
     * @param mixed $message
     * @param mixed $trace
     */
    public function testEmpty($message, $trace): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Error($message, $trace);
    }

    /**
     * @return array<mixed>
     */
    public function getCases(): array
    {
        return [
            'trace' => ['message', ''],
            'message' => ['', 'trace'],
            'all' => ['', ''],
        ];
    }
}
