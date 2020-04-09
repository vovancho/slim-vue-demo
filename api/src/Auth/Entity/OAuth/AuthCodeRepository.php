<?php

declare(strict_types=1);

namespace App\Auth\Entity\OAuth;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;

class AuthCodeRepository implements AuthCodeRepositoryInterface
{
    /**
     * @var ObjectRepository|EntityRepository
     */
    private ObjectRepository $repo;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(AuthCodeEntity::class);
        $this->em = $em;
    }

    public function getNewAuthCode(): AuthCodeEntityInterface
    {
        return new AuthCodeEntity();
    }

    public function persistNewAuthCode(AuthCodeEntityInterface $accessTokenEntity): void
    {
        if ($this->exists($accessTokenEntity->getIdentifier())) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }

        $this->em->persist($accessTokenEntity);
        $this->em->flush();
    }

    public function revokeAuthCode($tokenId): void
    {
        if ($token = $this->repo->find($tokenId)) {
            $this->em->remove($token);
            $this->em->flush();
        }
    }

    public function isAuthCodeRevoked($tokenId): bool
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
