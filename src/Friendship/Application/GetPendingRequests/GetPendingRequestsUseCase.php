<?php

declare(strict_types=1);

namespace DailyFocus\Friendship\Application\GetPendingRequests;

use DailyFocus\Friendship\Domain\FriendshipRepository;
use DailyFocus\Shared\Domain\ValueObjects\Id;

final class GetPendingRequestsUseCase
{
    private FriendshipRepository $friendshipRepository;

    public function __construct(FriendshipRepository $friendshipRepository)
    {
        $this->friendshipRepository = $friendshipRepository;
    }

    public function execute(GetPendingRequestsCommand $command): array
    {
        $id = Id::fromString($command->userId());
        
        return $this->friendshipRepository->findPendingFriendshipsForUser($id);
    }
}
