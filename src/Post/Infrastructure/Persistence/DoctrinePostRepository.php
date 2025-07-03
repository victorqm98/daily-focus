<?php

declare(strict_types=1);

namespace DailyFocus\Post\Infrastructure\Persistence;

use DailyFocus\Post\Domain\Post;
use DailyFocus\Post\Domain\PostRepository;
use DailyFocus\Shared\Domain\ValueObjects\Id;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrinePostRepository implements PostRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(Post $post): void
    {
        $this->entityManager->persist($post);
        $this->entityManager->flush();
    }

    public function findById(Id $id): ?Post
    {
        return $this->entityManager->find(Post::class, $id->value());
    }

    public function findTodaysPostByUser(Id $userId): ?Post
    {
        $today = new DateTimeImmutable();
        $startOfDay = $today->setTime(0, 0, 0);
        $endOfDay = $today->setTime(23, 59, 59);

        return $this->entityManager->getRepository(Post::class)
            ->createQueryBuilder('p')
            ->where('p.authorId = :userId')
            ->andWhere('p.createdAt >= :startOfDay')
            ->andWhere('p.createdAt <= :endOfDay')
            ->setParameter('userId', $userId->value())
            ->setParameter('startOfDay', $startOfDay)
            ->setParameter('endOfDay', $endOfDay)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findActiveFriendsPostsForUser(Id $userId, array $friendIds): array
    {
        if (empty($friendIds)) {
            return [];
        }

        $now = new DateTimeImmutable();
        $friendIdValues = array_map(fn(Id $id) => $id->value(), $friendIds);

        return $this->entityManager->getRepository(Post::class)
            ->createQueryBuilder('p')
            ->where('p.authorId IN (:friendIds)')
            ->andWhere('p.archivedAt > :now')
            ->setParameter('friendIds', $friendIdValues)
            ->setParameter('now', $now)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findArchivedPostsForUser(Id $userId): array
    {
        $now = new DateTimeImmutable();

        return $this->entityManager->getRepository(Post::class)
            ->createQueryBuilder('p')
            ->where('p.authorId = :userId')
            ->andWhere('p.archivedAt <= :now')
            ->setParameter('userId', $userId->value())
            ->setParameter('now', $now)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function hasUserPostedToday(Id $userId): bool
    {
        return $this->findTodaysPostByUser($userId) !== null;
    }
}
