<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\Token;

use App\Auth\Entity\User\Token;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Auth\Entity\User\Token::isExpiredTo
 */
class ExpiresTest extends TestCase
{
    public function testNot(): void
    {
        $token = new Token(
            $value = (string)rand(100000, 999999),
            $expires = new DateTimeImmutable()
        );

        self::assertFalse($token->isExpiredTo($expires->modify('-1 secs')));
        self::assertTrue($token->isExpiredTo($expires));
        self::assertTrue($token->isExpiredTo($expires->modify('+1 secs')));
    }
}
