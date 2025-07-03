<?php

declare(strict_types=1);

namespace DailyFocus\Friendship\Infrastructure\Persistence;

use DailyFocus\Friendship\Domain\Friendship;
use DailyFocus\Friendship\Domain\FriendshipRepository;
use DailyFocus\Shared\Domain\ValueObjects\Id;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineFriendshipRepository implements FriendshipRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(Friendship $friendship): void
    {
        $this->entityManager->persist($friendship);
        $this->entityManager->flush();
    }

    public function findById(Id $id): ?Friendship
    {
        return $this->entityManager->find(Friendship::class, $id->value());
    }

    public function findBetweenUsers(Id $userId1, Id $userId2): ?Friendship
    {
        return $this->entityManager->getRepository(Friendship::class)
            ->createQueryBuilder('f')
            ->where('(f.requesterId = :userId1 AND f.addresseeId = :userId2) OR (f.requesterId = :userId2 AND f.addresseeId = :userId1)')
            ->setParameter('userId1', $userId1->value())
            ->setParameter('userId2', $userId2->value())
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAcceptedFriendshipsForUser(Id $userId): array
    {
        return $this->entityManager->getRepository(Friendship::class)
            ->createQueryBuilder('f')
            ->where('(f.requesterId = :userId OR f.addresseeId = :userId) AND f.status = :status')
            ->setParameter('userId', $userId->value())
            ->setParameter('status', 'accepted')
            ->getQuery()
            ->getResult();
    }

    public function findPendingFriendshipsForUser(Id $userId): array
    {
        return $this->entityManager->getRepository(Friendship::class)
            ->createQueryBuilder('f')
            ->where('f.addresseeId = :userId AND f.status = :status')
            ->setParameter('userId', $userId->value())
            ->setParameter('status', 'pending')
            ->getQuery()
            ->getResult();
    }

    public function countAcceptedFriendshipsForUser(Id $userId): int
    {
        return (int) $this->entityManager->getRepository(Friendship::class)
            ->createQueryBuilder('f')
            ->select('COUNT(f.id)')
            ->where('(f.requesterId = :userId OR f.addresseeId = :userId) AND f.status = :status')
            ->setParameter('userId', $userId->value())
            ->setParameter('status', 'accepted')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
