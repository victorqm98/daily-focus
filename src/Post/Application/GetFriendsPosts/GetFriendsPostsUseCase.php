<?php

declare(strict_types=1);

namespace DailyFocus\Post\Application\GetFriendsPosts;

use DailyFocus\Post\Domain\PostRepository;
use DailyFocus\Friendship\Domain\FriendshipRepository;
use DailyFocus\Shared\Domain\ValueObjects\Id;
use InvalidArgumentException;

final class GetFriendsPostsUseCase
{
    private PostRepository $postRepository;
    private FriendshipRepository $friendshipRepository;

    public function __construct(
        PostRepository $postRepository,
        FriendshipRepository $friendshipRepository
    ) {
        $this->postRepository = $postRepository;
        $this->friendshipRepository = $friendshipRepository;
    }

    public function execute(GetFriendsPostsCommand $command): array
    {
        $userId = Id::fromString($command->userId());

        // El usuario debe haber posteado hoy para ver los posts de sus amigos
        if (!$this->postRepository->hasUserPostedToday($userId)) {
            throw new InvalidArgumentException('You must post today to see your friends\' posts');
        }

        // Obtener amigos aceptados
        $friendships = $this->friendshipRepository->findAcceptedFriendshipsForUser($userId);
        
        $friendIds = [];
        foreach ($friendships as $friendship) {
            if ($friendship->requesterId()->equals($userId)) {
                $friendIds[] = $friendship->addresseeId();
            } else {
                $friendIds[] = $friendship->requesterId();
            }
        }

        if (empty($friendIds)) {
            return [];
        }

        return $this->postRepository->findActiveFriendsPostsForUser($userId, $friendIds);
    }
}
