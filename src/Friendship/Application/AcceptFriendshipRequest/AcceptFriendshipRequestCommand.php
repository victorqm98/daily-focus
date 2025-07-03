<?php

declare(strict_types=1);

namespace DailyFocus\Friendship\Application\AcceptFriendshipRequest;

final class AcceptFriendshipRequestCommand
{
    private string $friendshipId;
    private string $userId;

    public function __construct(string $friendshipId, string $userId)
    {
        $this->friendshipId = $friendshipId;
        $this->userId = $userId;
    }

    public function friendshipId(): string
    {
        return $this->friendshipId;
    }

    public function userId(): string
    {
        return $this->userId;
    }
}
