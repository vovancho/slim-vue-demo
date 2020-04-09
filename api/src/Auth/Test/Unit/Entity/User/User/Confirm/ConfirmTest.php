<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User\Confirm;

use App\Auth\Entity\User\Token;
use PHPUnit\Framework\TestCase;
use App\Auth\Test\Builder\UserBuilder;
use Ramsey\Uuid\Uuid;

/**
 * @covers \App\Auth\Entity\User\User
 */
class ConfirmTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())
            ->withConfirmToken($token = $this->createToken())
            ->build();

        self::assertTrue($user->isWait());
        self::assertFalse($user->isActive());

        $user->confirmSignUp(
            $token->getValue(),
            $token->getExpires()->modify('-1 day')
        );

        self::assertFalse($user->isWait());
        self::assertTrue($user->isActive());

        self::assertNull($user->getConfirmToken());
    }

    public function testWrong(): void
    {
        $user = (new UserBuilder())
            ->withConfirmToken($token = $this->createToken())
            ->build();

        $this->expectExceptionMessage('Неверный код подтверждения.');

        $user->confirmSignUp(
            Uuid::uuid4()->toString(),
            $token->getExpires()->modify('-1 day')
        );
    }

    public function testExpired(): void
    {
        $user = (new UserBuilder())
            ->withConfirmToken($token = $this->createToken())
            ->build();

        $this->expectExceptionMessage('Код подтверждения истек.');

        $user->confirmSignUp(
            $token->getValue(),
            $token->getExpires()->modify('+1 day')
        );
    }

    public function testAlready(): void
    {
        $token = $this->createToken();

        $user = (new UserBuilder())
            ->withConfirmToken($token)
            ->active()
            ->build();

        $this->expectExceptionMessage('Токен подтверждения обязателен.');

        $user->confirmSignUp(
            $token->getValue(),
            $token->getExpires()->modify('-1 day')
        );
    }

    private function createToken(): Token
    {
        return new Token(
            (string)rand(100000, 999999),
            new \DateTimeImmutable('+1 day')
        );
    }
}
