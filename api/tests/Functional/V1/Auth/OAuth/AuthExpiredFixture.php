<?php

declare(strict_types=1);

namespace App\Test\Functional\V1\Auth\OAuth;

use App\Auth\Entity\OAuth\AccessTokenEntity;
use App\Auth\Entity\OAuth\ClientEntity;
use App\Auth\Entity\OAuth\ScopeEntity;
use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use App\Auth\Service\PasswordHasher;
use App\Test\Functional\CryptKeyHelper;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class AuthExpiredFixture extends AbstractFixture
{
    private User $user;
    private string $token;

    public function load(ObjectManager $manager): void
    {
        $hasher = new PasswordHasher();

        $user = User::requestForConfirm(
            Id::generate(),
            $date = new DateTimeImmutable(),
            new Email('oauth@app.test'),
            $hasher->hash('password-hash'),
            new Token($confirmToken = (string)rand(100000, 999999), $date->modify('+1 hour'))
        );

        $user->confirmSignup($confirmToken, new DateTimeImmutable());

        $manager->persist($user);

        $this->user = $user;

        $token = new AccessTokenEntity();
        $token->setIdentifier(bin2hex(random_bytes(40)));
        $token->setUserIdentifier($user->getId()->getValue());
        $token->setExpiryDateTime(new DateTimeImmutable('-2 hours'));
        $token->setClient(new ClientEntity('app'));
        $token->addScope(new ScopeEntity('common'));
        $token->setPrivateKey(CryptKeyHelper::get());

        $manager->persist($token);

        $manager->flush();

        $this->addReference('user', $user);

        $this->token = (string)$token;
    }

    public function getHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->token,
        ];
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
