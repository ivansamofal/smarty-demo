<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use App\Entity\Post;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class PostTest extends TestCase
{
    public function testWithViewsReturnsNewInstanceWithUpdatedViews(): void
    {
        $publishedAt = new DateTimeImmutable('2026-01-01 10:00:00');
        $post = new Post(1, 'Title', 'slug', 'description', 'content', '/image.svg', 5, $publishedAt);

        $updated = $post->withViews(6);

        self::assertSame(5, $post->views);
        self::assertSame(6, $updated->views);
        self::assertNotSame($post, $updated);
        self::assertSame($post->title, $updated->title);
        self::assertSame($post->publishedAt, $updated->publishedAt);
    }
}
