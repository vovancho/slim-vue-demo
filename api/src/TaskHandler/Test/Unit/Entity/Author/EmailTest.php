<?php

declare(strict_types=1);

namespace App\TaskHandler\Test\Unit\Entity\Author;

use App\TaskHandler\Entity\Author\Email;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\TaskHandler\Entity\Author\Email
 */
class EmailTest extends TestCase
{
    public function testSuccess(): void
    {
        $email = new Email($value = 'email@app.test');

        self::assertEquals($value, $email->getValue());
    }

    public function testCase(): void
    {
        $email = new Email('EmAil@app.test');

        self::assertEquals('email@app.test', $email->getValue());
    }

    public function testIncorrect(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Email('not-email');
    }

    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Email('');
    }
}
