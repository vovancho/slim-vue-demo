<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;
use DomainException;

class UserRepository
{
    /**
     * @var ObjectRepository|EntityRepository
     */
    private ObjectRepository $repo;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(User::class);
        $this->em = $em;
    }

    public function hasByEmail(Email $email): bool
    {
        return $this->repo->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->andWhere('t.email = :email')
                ->setParameter(':email', $email->getValue())
                ->getQuery()->getSingleScalarResult() > 0;
    }

    public function getByEmail(Email $email): User
    {
        /** @var User $user */
        if (!$user = $this->repo->findOneBy(['email' => $email->getValue()])) {
            throw new DomainException('Пользователь не найден.');
        }
        return $user;
    }

    public function getEmailById(Id $id): Email
    {
        /** @var User $user */
        if (!$user = $this->repo->findOneBy(['id' => $id->getValue()])) {
            throw new DomainException('Пользователь не найден.');
        }
        return $user->getEmail();
    }

    public function get(Id $id): User
    {
        /** @var User $user */
        if (!$user = $this->repo->find($id->getValue())) {
            throw new DomainException('Пользователь не найден.');
        }
        return $user;
    }

    public function add(User $user): void
    {
        $this->em->persist($user);
    }
}
