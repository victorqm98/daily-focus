<?php

declare(strict_types=1);

namespace DailyFocus\Friendship\Application\GetPendingRequests;

final class GetPendingRequestsCommand
{
    private string $userId;

    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    public function userId(): string
    {
        return $this->userId;
    }
}
