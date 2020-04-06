<?php

declare(strict_types=1);

namespace App\Test\Functional\V1\Auth\SignUp;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class ConfirmFixture extends AbstractFixture
{
    public const VALID = '000001';
    public const EXPIRED = '000002';
    public const INVALID = '000002';

    public function load(ObjectManager $manager): void
    {
        // Valid

        $user = User::requestForConfirm(
            Id::generate(),
            $date = new DateTimeImmutable(),
            new Email('valid@app.test'),
            'password-hash',
            new Token($value = self::VALID, $date->modify('+1 hour'))
        );

        $manager->persist($user);

        // Expired

        $user = User::requestForConfirm(
            Id::generate(),
            $date = new DateTimeImmutable(),
            new Email('expired@app.test'),
            'password-hash',
            new Token($value = self::EXPIRED, $date->modify('-2 hours'))
        );

        $manager->persist($user);

        $manager->flush();
    }
}
