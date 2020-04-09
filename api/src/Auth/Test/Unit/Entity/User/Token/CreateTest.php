<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\Token;

use App\Auth\Entity\User\Token;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Auth\Entity\User\Token
 */
class CreateTest extends TestCase
{
    public function testSuccess(): void
    {
        $token = new Token(
            $value = (string)rand(100000, 999999),
            $expires = new DateTimeImmutable()
        );

        self::assertEquals($value, $token->getValue());
        self::assertEquals($expires, $token->getExpires());
    }

    public function testIncorrectMinLength(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Token('12345', new DateTimeImmutable());
    }

    public function testIncorrectMaxLength(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Token('1234567', new DateTimeImmutable());
    }

    public function testIncorrectDigits(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Token('123abc', new DateTimeImmutable());
    }

    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Token('', new DateTimeImmutable());
    }
}
