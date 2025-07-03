<?php

declare(strict_types=1);

namespace DailyFocus\Tests\Post\Domain;

use DailyFocus\Post\Domain\Post;
use DailyFocus\Shared\Domain\ValueObjects\Id;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

final class PostTest extends TestCase
{
    public function test_post_can_be_created(): void
    {
        $postId = Id::random();
        $authorId = Id::random();
        $content = 'This is my daily thoughts...';

        $post = Post::create($postId, $authorId, $content);

        $this->assertEquals($postId, $post->id());
        $this->assertEquals($authorId, $post->authorId());
        $this->assertEquals($content, $post->content());
        $this->assertTrue($post->isActive());
        $this->assertFalse($post->isArchived());
    }

    public function test_post_cannot_be_created_with_empty_content(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Post content cannot be empty');

        $postId = Id::random();
        $authorId = Id::random();
        $content = '';

        Post::create($postId, $authorId, $content);
    }

    public function test_post_is_archived_after_24_hours(): void
    {
        $postId = Id::random();
        $authorId = Id::random();
        $content = 'This is my daily thoughts...';

        $post = Post::create($postId, $authorId, $content);

        // El post debería archivarse 24 horas después de su creación
        $this->assertEquals(
            $post->createdAt()->modify('+24 hours'),
            $post->archivedAt()
        );
    }
}
