<?php

declare(strict_types=1);

namespace DailyFocus\Post\Application\CreatePost;

final class CreatePostCommand
{
    private string $authorId;
    private string $content;

    public function __construct(string $authorId, string $content)
    {
        $this->authorId = $authorId;
        $this->content = $content;
    }

    public function authorId(): string
    {
        return $this->authorId;
    }

    public function content(): string
    {
        return $this->content;
    }
}
