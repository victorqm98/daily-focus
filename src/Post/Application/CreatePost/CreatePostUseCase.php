<?php

declare(strict_types=1);

namespace DailyFocus\Post\Application\CreatePost;

use DailyFocus\Post\Domain\Post;
use DailyFocus\Post\Domain\PostRepository;
use DailyFocus\Shared\Domain\ValueObjects\Id;
use InvalidArgumentException;

final class CreatePostUseCase
{
    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function execute(CreatePostCommand $command): void
    {
        $authorId = Id::fromString($command->authorId());

        if ($this->postRepository->hasUserPostedToday($authorId)) {
            throw new InvalidArgumentException('You can only post once per day');
        }

        $post = Post::create(
            Id::random(),
            $authorId,
            $command->content()
        );

        $this->postRepository->save($post);
    }
}
