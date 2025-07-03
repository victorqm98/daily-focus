<?php

declare(strict_types=1);

namespace DailyFocus\Post\Domain;

use DailyFocus\Shared\Domain\ValueObjects\Id;
use DateTimeImmutable;
use InvalidArgumentException;

final class Post
{
    private Id $id;
    private Id $authorId;
    private string $content;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $archivedAt;

    public function __construct(
        Id $id,
        Id $authorId,
        string $content
    ) {
        $this->ensureContentIsNotEmpty($content);
        
        $this->id = $id;
        $this->authorId = $authorId;
        $this->content = $content;
        $this->createdAt = new DateTimeImmutable();
        $this->archivedAt = $this->createdAt->modify('+24 hours');
    }

    public static function create(
        Id $id,
        Id $authorId,
        string $content
    ): self {
        return new self($id, $authorId, $content);
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function authorId(): Id
    {
        return $this->authorId;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function archivedAt(): DateTimeImmutable
    {
        return $this->archivedAt;
    }

    public function isActive(): bool
    {
        return new DateTimeImmutable() < $this->archivedAt;
    }

    public function isArchived(): bool
    {
        return !$this->isActive();
    }

    private function ensureContentIsNotEmpty(string $content): void
    {
        if (trim($content) === '') {
            throw new InvalidArgumentException('Post content cannot be empty');
        }
    }
}
