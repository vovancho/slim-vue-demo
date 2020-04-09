<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User\Confirm;

use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\User;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Auth\Entity\User\User
 */
class RequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = User::requestForConfirm(
            $id = Id::generate(),
            $date = new DateTimeImmutable(),
            $email = new Email('mail@example.com'),
            $hash = 'hash',
            $token = new Token((string)rand(100000, 999999), new DateTimeImmutable())
        );

        self::assertEquals($id, $user->getId());
        self::assertEquals($date, $user->getDate());
        self::assertEquals($email, $user->getEmail());
        self::assertEquals($hash, $user->getPasswordHash());
        self::assertEquals($token, $user->getConfirmToken());

        self::assertTrue($user->isWait());
        self::assertFalse($user->isActive());
    }
}
