<?php

declare(strict_types=1);

namespace DailyFocus\Tests\Post\Application;

use DailyFocus\Post\Application\GetFriendsPosts\GetFriendsPostsUseCase;
use DailyFocus\Post\Application\GetFriendsPosts\GetFriendsPostsCommand;
use DailyFocus\Post\Domain\PostRepository;
use DailyFocus\Friendship\Domain\FriendshipRepository;
use DailyFocus\Shared\Domain\ValueObjects\Id;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use InvalidArgumentException;

final class GetFriendsPostsUseCaseTest extends TestCase
{
    /** @var PostRepository&MockObject */
    private $postRepository;
    
    /** @var FriendshipRepository&MockObject */
    private $friendshipRepository;
    
    private GetFriendsPostsUseCase $useCase;

    protected function setUp(): void
    {
        $this->postRepository = $this->createMock(PostRepository::class);
        $this->friendshipRepository = $this->createMock(FriendshipRepository::class);
        $this->useCase = new GetFriendsPostsUseCase(
            $this->postRepository,
            $this->friendshipRepository
        );
    }

    public function test_user_must_post_today_to_see_friends_posts(): void
    {
        $userId = Id::random();
        $command = new GetFriendsPostsCommand($userId->value());

        $this->postRepository
            ->expects($this->once())
            ->method('hasUserPostedToday')
            ->with($userId)
            ->willReturn(false);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('You must post today to see your friends\' posts');

        $this->useCase->execute($command);
    }

    public function test_returns_empty_array_when_user_has_no_friends(): void
    {
        $userId = Id::random();
        $command = new GetFriendsPostsCommand($userId->value());

        $this->postRepository
            ->expects($this->once())
            ->method('hasUserPostedToday')
            ->with($userId)
            ->willReturn(true);

        $this->friendshipRepository
            ->expects($this->once())
            ->method('findAcceptedFriendshipsForUser')
            ->with($userId)
            ->willReturn([]);

        $result = $this->useCase->execute($command);

        $this->assertEquals([], $result);
    }
}
