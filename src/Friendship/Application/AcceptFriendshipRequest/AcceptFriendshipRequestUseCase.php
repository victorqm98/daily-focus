<?php

declare(strict_types=1);

namespace DailyFocus\Friendship\Application\AcceptFriendshipRequest;

use DailyFocus\Friendship\Domain\FriendshipRepository;
use DailyFocus\Shared\Domain\ValueObjects\Id;
use InvalidArgumentException;

final class AcceptFriendshipRequestUseCase
{
    private FriendshipRepository $friendshipRepository;

    public function __construct(FriendshipRepository $friendshipRepository)
    {
        $this->friendshipRepository = $friendshipRepository;
    }

    public function execute(AcceptFriendshipRequestCommand $command): void
    {
        $friendshipId = Id::fromString($command->friendshipId());
        $userId = Id::fromString($command->userId());

        $friendship = $this->friendshipRepository->findById($friendshipId);
        
        if ($friendship === null) {
            throw new InvalidArgumentException('Friendship request not found');
        }

        // Solo el destinatario puede aceptar la solicitud
        if (!$friendship->addresseeId()->equals($userId)) {
            throw new InvalidArgumentException('You can only accept friendship requests sent to you');
        }

        if (!$friendship->isPending()) {
            throw new InvalidArgumentException('Friendship request is not pending');
        }

        $friendship->accept();
        
        $this->friendshipRepository->save($friendship);
    }
}
