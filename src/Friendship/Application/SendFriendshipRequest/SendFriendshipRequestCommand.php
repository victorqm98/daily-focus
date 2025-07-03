<?php

declare(strict_types=1);

namespace DailyFocus\Friendship\Application\SendFriendshipRequest;

final class SendFriendshipRequestCommand
{
    private string $requesterId;
    private string $addresseeId;

    public function __construct(string $requesterId, string $addresseeId)
    {
        $this->requesterId = $requesterId;
        $this->addresseeId = $addresseeId;
    }

    public function requesterId(): string
    {
        return $this->requesterId;
    }

    public function addresseeId(): string
    {
        return $this->addresseeId;
    }
}
