<?php

declare(strict_types=1);

namespace Tests\Integration\Service;

use App\Exception\PostNotFoundException;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Service\PostService;
use Tests\Integration\IntegrationTestCase;

final class PostServiceTest extends IntegrationTestCase
{
    private PostService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new PostService(
            new PostRepository($this->connection),
            new CategoryRepository($this->connection),
        );
    }

    public function testGetPostPageThrowsWhenMissing(): void
    {
        $this->expectException(PostNotFoundException::class);

        $this->service->getPostPage('missing');
    }

    public function testGetPostPageIncrementsViewsAndLoadsCategoriesAndSimilar(): void
    {
        $phpId = $this->insertCategory('PHP', 'php');
        $postId = $this->insertPost('Пост', 'shared', '2026-01-05 10:00:00', 10);
        $this->linkPostCategory($postId, $phpId);

        $otherId = $this->insertPost('Похожий', 'similar', '2026-01-04 10:00:00');
        $this->linkPostCategory($otherId, $phpId);

        $page = $this->service->getPostPage('shared');

        self::assertSame(11, $page->post->views);
        self::assertSame(['php'], array_map(static fn ($category) => $category->slug, $page->categories));
        self::assertSame(['similar'], array_map(static fn ($post) => $post->slug, $page->similarPosts));
    }
}
