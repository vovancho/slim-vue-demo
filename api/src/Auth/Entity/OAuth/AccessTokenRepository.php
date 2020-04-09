<?php

declare(strict_types=1);

namespace App\Auth\Entity\OAuth;

use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    /**
     * @var ObjectRepository|EntityRepository
     */
    private ObjectRepository $repo;
    private EntityManagerInterface $em;
    private UserRepository $userRepository;

    public function __construct(EntityManagerInterface $em, UserRepository $userRepository)
    {
        $this->repo = $em->getRepository(AccessTokenEntity::class);
        $this->em = $em;
        $this->userRepository = $userRepository;
    }

    public function getNewToken(
        ClientEntityInterface $clientEntity,
        array $scopes,
        $userIdentifier = null
    ): AccessTokenEntityInterface {
        $accessToken = new AccessTokenEntity();
        if ($userIdentifier) {
            $email = $this->userRepository->getEmailById(new Id((string)$userIdentifier));
            $accessToken->setUserEmail($email->getValue());
        }
        $accessToken->setClient($clientEntity);
        foreach ($scopes as $scope) {
            $accessToken->addScope($scope);
        }
        $accessToken->setUserIdentifier($userIdentifier);
        return $accessToken;
    }

    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
        if ($this->exists($accessTokenEntity->getIdentifier())) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }

        $this->em->persist($accessTokenEntity);
        $this->em->flush();
    }

    public function revokeAccessToken($tokenId): void
    {
        if ($token = $this->repo->find($tokenId)) {
            $this->em->remove($token);
            $this->em->flush();
        }
    }

    public function isAccessTokenRevoked($tokenId): bool
    {
        return !$this->exists($tokenId);
    }

    private function exists(string $id): bool
    {
        return $this->repo->createQueryBuilder('t')
                ->select('COUNT(t.identifier)')
                ->andWhere('t.identifier = :identifier')
                ->setParameter(':identifier', $id)
                ->getQuery()->getSingleScalarResult() > 0;
    }
}
