<?php

declare(strict_types=1);

namespace DailyFocus\Friendship\Domain;

use DailyFocus\Shared\Domain\ValueObjects\Id;
use DateTimeImmutable;

enum FriendshipStatus: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
}

final class Friendship
{
    private Id $id;
    private Id $requesterId;
    private Id $addresseeId;
    private FriendshipStatus $status;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        Id $id,
        Id $requesterId,
        Id $addresseeId,
        FriendshipStatus $status = FriendshipStatus::PENDING
    ) {
        $this->id = $id;
        $this->requesterId = $requesterId;
        $this->addresseeId = $addresseeId;
        $this->status = $status;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public static function request(
        Id $id,
        Id $requesterId,
        Id $addresseeId
    ): self {
        return new self($id, $requesterId, $addresseeId, FriendshipStatus::PENDING);
    }

    public function accept(): void
    {
        $this->status = FriendshipStatus::ACCEPTED;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function reject(): void
    {
        $this->status = FriendshipStatus::REJECTED;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function requesterId(): Id
    {
        return $this->requesterId;
    }

    public function addresseeId(): Id
    {
        return $this->addresseeId;
    }

    public function status(): FriendshipStatus
    {
        return $this->status;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function isAccepted(): bool
    {
        return $this->status === FriendshipStatus::ACCEPTED;
    }

    public function isPending(): bool
    {
        return $this->status === FriendshipStatus::PENDING;
    }

    public function involvesUser(Id $userId): bool
    {
        return $this->requesterId->equals($userId) || $this->addresseeId->equals($userId);
    }
}
