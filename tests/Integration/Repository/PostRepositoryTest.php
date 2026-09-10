<?php

declare(strict_types=1);

namespace Tests\Integration\Repository;

use App\Repository\PostRepository;
use App\Support\PostSort;
use Tests\Integration\IntegrationTestCase;

final class PostRepositoryTest extends IntegrationTestCase
{
    public function testFindLatestByCategoriesReturnsMostRecentFirst(): void
    {
        $categoryId = $this->insertCategory('PHP', 'php');
        $old = $this->insertPost('Старый', 'old', '2026-01-01 10:00:00');
        $new = $this->insertPost('Новый', 'new', '2026-01-05 10:00:00');
        $this->linkPostCategory($old, $categoryId);
        $this->linkPostCategory($new, $categoryId);

        $repository = new PostRepository($this->connection);
        $postsByCategory = $repository->findLatestByCategories([$categoryId], 10);

        self::assertSame(
            ['new', 'old'],
            array_map(static fn ($post) => $post->slug, $postsByCategory[$categoryId]),
        );
    }

    public function testFindLatestByCategoriesRespectsLimit(): void
    {
        $categoryId = $this->insertCategory('PHP', 'php');

        for ($i = 1; $i <= 5; $i++) {
            $postId = $this->insertPost("Пост $i", "post-$i", sprintf('2026-01-%02d 10:00:00', $i));
            $this->linkPostCategory($postId, $categoryId);
        }

        $repository = new PostRepository($this->connection);
        $postsByCategory = $repository->findLatestByCategories([$categoryId], 3);

        self::assertCount(3, $postsByCategory[$categoryId]);
    }

    public function testFindLatestByCategoriesBatchesMultipleCategoriesInOneCall(): void
    {
        $phpId = $this->insertCategory('PHP', 'php');
        $jsId = $this->insertCategory('JavaScript', 'javascript');

        $phpOld = $this->insertPost('PHP старый', 'php-old', '2026-01-01 10:00:00');
        $phpNew = $this->insertPost('PHP новый', 'php-new', '2026-01-05 10:00:00');
        $this->linkPostCategory($phpOld, $phpId);
        $this->linkPostCategory($phpNew, $phpId);

        $jsPost = $this->insertPost('JS пост', 'js-post', '2026-01-03 10:00:00');
        $this->linkPostCategory($jsPost, $jsId);

        $repository = new PostRepository($this->connection);
        $postsByCategory = $repository->findLatestByCategories([$phpId, $jsId], 10);

        self::assertSame(
            ['php-new', 'php-old'],
            array_map(static fn ($post) => $post->slug, $postsByCategory[$phpId]),
        );
        self::assertSame(
            ['js-post'],
            array_map(static fn ($post) => $post->slug, $postsByCategory[$jsId]),
        );
    }

    public function testFindLatestByCategoriesReturnsEmptyArrayForNoCategories(): void
    {
        $repository = new PostRepository($this->connection);

        self::assertSame([], $repository->findLatestByCategories([], 3));
    }

    public function testFindByCategoryPaginatedSortsByViewsDescending(): void
    {
        $categoryId = $this->insertCategory('PHP', 'php');
        $low = $this->insertPost('Low', 'low', '2026-01-01 10:00:00', 5);
        $high = $this->insertPost('High', 'high', '2026-01-02 10:00:00', 50);
        $this->linkPostCategory($low, $categoryId);
        $this->linkPostCategory($high, $categoryId);

        $repository = new PostRepository($this->connection);
        $page = $repository->findByCategoryPaginated($categoryId, PostSort::VIEWS, 1, 10);

        self::assertSame(2, $page->total);
        self::assertSame(['high', 'low'], array_map(static fn ($post) => $post->slug, $page->items));
    }

    public function testFindByCategoryPaginatedPagesResults(): void
    {
        $categoryId = $this->insertCategory('PHP', 'php');

        for ($i = 1; $i <= 5; $i++) {
            $postId = $this->insertPost("Пост $i", "post-$i", sprintf('2026-01-%02d 10:00:00', $i));
            $this->linkPostCategory($postId, $categoryId);
        }

        $repository = new PostRepository($this->connection);
        $page = $repository->findByCategoryPaginated($categoryId, PostSort::DATE, 2, 2);

        self::assertSame(5, $page->total);
        self::assertSame(3, $page->totalPages());
        self::assertSame(['post-3', 'post-2'], array_map(static fn ($post) => $post->slug, $page->items));
    }

    public function testFindBySlugReturnsNullWhenMissing(): void
    {
        $repository = new PostRepository($this->connection);

        self::assertNull($repository->findBySlug('missing'));
    }

    public function testFindSimilarReturnsEmptyArrayWhenNoCategories(): void
    {
        $repository = new PostRepository($this->connection);

        self::assertSame([], $repository->findSimilar(1, [], 3));
    }

    public function testFindSimilarRanksBySharedCategoriesThenDate(): void
    {
        $phpId = $this->insertCategory('PHP', 'php');
        $jsId = $this->insertCategory('JavaScript', 'javascript');

        $current = $this->insertPost('Текущий', 'current', '2026-01-10 10:00:00');
        $this->linkPostCategory($current, $phpId);
        $this->linkPostCategory($current, $jsId);

        $bothShared = $this->insertPost('Оба', 'both-shared', '2026-01-01 10:00:00');
        $this->linkPostCategory($bothShared, $phpId);
        $this->linkPostCategory($bothShared, $jsId);

        $oneSharedNewer = $this->insertPost('Один новее', 'one-shared-newer', '2026-01-05 10:00:00');
        $this->linkPostCategory($oneSharedNewer, $phpId);

        $oneSharedOlder = $this->insertPost('Один старее', 'one-shared-older', '2026-01-02 10:00:00');
        $this->linkPostCategory($oneSharedOlder, $phpId);

        $this->insertPost('Не связан', 'unrelated', '2026-01-09 10:00:00');

        $repository = new PostRepository($this->connection);
        $similar = $repository->findSimilar($current, [$phpId, $jsId], 3);

        self::assertSame(
            ['both-shared', 'one-shared-newer', 'one-shared-older'],
            array_map(static fn ($post) => $post->slug, $similar),
        );
    }

    public function testIncrementViewsIncreasesCountByOne(): void
    {
        $postId = $this->insertPost('Пост', 'post', '2026-01-01 10:00:00', 5);
        $repository = new PostRepository($this->connection);

        $repository->incrementViews($postId);

        self::assertSame(6, $repository->findBySlug('post')?->views);
    }
}
