<?php

declare(strict_types=1);

namespace App\Auth\Entity\OAuth;

use App\Auth\Entity\User\User;
use App\Auth\Service\PasswordHasher;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

class OAuthRepository implements UserRepositoryInterface
{
    /**
     * @var ObjectRepository|EntityRepository
     */
    private ObjectRepository $repo;
    /**
     * @var PasswordHasher
     */
    private PasswordHasher $hasher;

    public function __construct(EntityManagerInterface $em, PasswordHasher $hasher)
    {
        $this->repo = $em->getRepository(User::class);
        $this->hasher = $hasher;
    }

    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ): ?UserEntityInterface {
        /** @var User $user */
        if ($user = $this->repo->findOneBy(['email' => $username])) {
            if (!$this->hasher->validate($password, $user->getPasswordHash())) {
                return null;
            }
            return new UserEntity($user->getId()->getValue());
        }
        return null;
    }
}
