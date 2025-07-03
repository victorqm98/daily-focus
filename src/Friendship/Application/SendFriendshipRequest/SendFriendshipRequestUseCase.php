<?php

declare(strict_types=1);

namespace DailyFocus\Friendship\Application\SendFriendshipRequest;

use DailyFocus\Friendship\Domain\Friendship;
use DailyFocus\Friendship\Domain\FriendshipRepository;
use DailyFocus\Shared\Domain\ValueObjects\Id;
use InvalidArgumentException;

final class SendFriendshipRequestUseCase
{
    private const MAX_FRIENDS = 5;
    
    private FriendshipRepository $friendshipRepository;

    public function __construct(FriendshipRepository $friendshipRepository)
    {
        $this->friendshipRepository = $friendshipRepository;
    }

    public function execute(SendFriendshipRequestCommand $command): void
    {
        $requesterId = Id::fromString($command->requesterId());
        $addresseeId = Id::fromString($command->addresseeId());

        // No puedes enviarte solicitud a ti mismo
        if ($requesterId->equals($addresseeId)) {
            throw new InvalidArgumentException('Cannot send friendship request to yourself');
        }

        // Verificar si ya existe una amistad entre estos usuarios
        $existingFriendship = $this->friendshipRepository->findBetweenUsers($requesterId, $addresseeId);
        if ($existingFriendship !== null) {
            throw new InvalidArgumentException('Friendship already exists between these users');
        }

        // Verificar límite de amigos para el solicitante
        $requesterFriendsCount = $this->friendshipRepository->countAcceptedFriendshipsForUser($requesterId);
        if ($requesterFriendsCount >= self::MAX_FRIENDS) {
            throw new InvalidArgumentException('Maximum number of friends reached');
        }

        // Verificar límite de amigos para el destinatario
        $addresseeFriendsCount = $this->friendshipRepository->countAcceptedFriendshipsForUser($addresseeId);
        if ($addresseeFriendsCount >= self::MAX_FRIENDS) {
            throw new InvalidArgumentException('User has reached maximum number of friends');
        }

        $friendship = Friendship::request(
            Id::random(),
            $requesterId,
            $addresseeId
        );

        $this->friendshipRepository->save($friendship);
    }
}
