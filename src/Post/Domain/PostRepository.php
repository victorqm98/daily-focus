<?php

declare(strict_types=1);

namespace DailyFocus\Post\Domain;

use DailyFocus\Shared\Domain\ValueObjects\Id;
use DateTimeImmutable;

interface PostRepository
{
    public function save(Post $post): void;
    
    public function findById(Id $id): ?Post;
    
    public function findTodaysPostByUser(Id $userId): ?Post;
    
    public function findActiveFriendsPostsForUser(Id $userId, array $friendIds): array;
    
    public function findArchivedPostsForUser(Id $userId): array;
    
    public function hasUserPostedToday(Id $userId): bool;
}
