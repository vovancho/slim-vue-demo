<?php

declare(strict_types=1);

namespace Api\Data\Fixture;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use App\Auth\Service\PasswordHasher;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $hasher = new PasswordHasher();

        $user = User::requestForConfirm(
            Id::generate(),
            $date = new DateTimeImmutable(),
            new Email('user@app.dev'),
            $hasher->hash('secret'),
            $token = new Token($value = (string)rand(100000, 999999), $date->modify('+1 day'))
        );

        $user->confirmSignUp($token->getValue(), new DateTimeImmutable());

        $manager->persist($user);
        $manager->flush();

        $this->addReference('user', $user);
    }
}
