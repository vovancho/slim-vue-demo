<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;

class UserReadRepository
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

    /**
     * @param string $id
     * @return User|null|object
     */
    public function find(string $id): ?User
    {
        return $this->repo->find($id);
    }
}
