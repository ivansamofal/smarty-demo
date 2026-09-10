<?php

declare(strict_types=1);

namespace Tests\Integration\Repository;

use App\Repository\CategoryRepository;
use Tests\Integration\IntegrationTestCase;

final class CategoryRepositoryTest extends IntegrationTestCase
{
    public function testFindAllWithPostsExcludesEmptyCategories(): void
    {
        $repository = new CategoryRepository($this->connection);

        $phpId = $this->insertCategory('PHP', 'php');
        $this->insertCategory('Пустая', 'empty');
        $postId = $this->insertPost('Пост', 'post-1', '2026-01-01 10:00:00');
        $this->linkPostCategory($postId, $phpId);

        $categories = $repository->findAllWithPosts();

        self::assertCount(1, $categories);
        self::assertSame('php', $categories[0]->slug);
    }

    public function testFindBySlugReturnsNullWhenMissing(): void
    {
        $repository = new CategoryRepository($this->connection);

        self::assertNull($repository->findBySlug('does-not-exist'));
    }

    public function testFindBySlugReturnsCategory(): void
    {
        $this->insertCategory('PHP', 'php', 'Описание');
        $repository = new CategoryRepository($this->connection);

        $category = $repository->findBySlug('php');

        self::assertNotNull($category);
        self::assertSame('PHP', $category->name);
        self::assertSame('Описание', $category->description);
    }

    public function testFindByPostIdReturnsLinkedCategoriesOrderedByName(): void
    {
        $jsId = $this->insertCategory('JavaScript', 'javascript');
        $phpId = $this->insertCategory('PHP', 'php');
        $postId = $this->insertPost('Пост', 'post-1', '2026-01-01 10:00:00');
        $this->linkPostCategory($postId, $phpId);
        $this->linkPostCategory($postId, $jsId);

        $repository = new CategoryRepository($this->connection);
        $categories = $repository->findByPostId($postId);

        self::assertSame(['JavaScript', 'PHP'], array_map(static fn ($category) => $category->name, $categories));
    }
}
