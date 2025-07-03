<?php

declare(strict_types=1);

namespace DailyFocus\Friendship\Domain;

use DailyFocus\Shared\Domain\ValueObjects\Id;

interface FriendshipRepository
{
    public function save(Friendship $friendship): void;

    public function findById(Id $id): ?Friendship;

    public function findBetweenUsers(Id $userId1, Id $userId2): ?Friendship;

    public function findAcceptedFriendshipsForUser(Id $userId): array;

    public function findPendingFriendshipsForUser(Id $userId): array;

    public function countAcceptedFriendshipsForUser(Id $userId): int;
}
