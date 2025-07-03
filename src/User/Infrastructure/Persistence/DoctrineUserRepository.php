<?php

declare(strict_types=1);

namespace DailyFocus\User\Infrastructure\Persistence;

use DailyFocus\User\Domain\User;
use DailyFocus\User\Domain\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineUserRepository implements UserRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function findById(string $id): ?User
    {
        return $this->entityManager->getRepository(User::class)
            ->findOneBy(['id.value' => $id]);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->entityManager->getRepository(User::class)
            ->findOneBy(['email.value' => $email]);
    }

    public function findByUsername(string $username): ?User
    {
        return $this->entityManager->getRepository(User::class)
            ->findOneBy(['username' => $username]);
    }

    public function searchByUsername(string $username): array
    {
        return $this->entityManager->getRepository(User::class)
            ->createQueryBuilder('u')
            ->where('u.username LIKE :username')
            ->setParameter('username', '%' . $username . '%')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
