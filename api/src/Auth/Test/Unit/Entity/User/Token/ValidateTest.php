<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\Token;

use App\Auth\Entity\User\Token;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @covers \App\Auth\Entity\User\Token::validate
 */
class ValidateTest extends TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testSuccess(): void
    {
        $token = new Token(
            $value = (string)rand(100000, 999999),
            $expires = new DateTimeImmutable()
        );

        $token->validate($value, $expires->modify('-1 secs'));
    }

    public function testWrong(): void
    {
        $token = new Token(
            $value = (string)rand(100000, 999999),
            $expires = new DateTimeImmutable()
        );

        $this->expectExceptionMessage('Неверный код подтверждения.');
        $token->validate(Uuid::uuid4()->toString(), $expires->modify('-1 secs'));
    }

    public function testExpired(): void
    {
        $token = new Token(
            $value = (string)rand(100000, 999999),
            $expires = new DateTimeImmutable()
        );

        $this->expectExceptionMessage('Код подтверждения истек.');
        $token->validate($value, $expires->modify('+1 secs'));
    }
}
