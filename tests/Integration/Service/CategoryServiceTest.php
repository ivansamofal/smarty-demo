<?php

declare(strict_types=1);

namespace Tests\Integration\Service;

use App\Exception\CategoryNotFoundException;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Service\CategoryService;
use App\Support\PostSort;
use Tests\Integration\IntegrationTestCase;

final class CategoryServiceTest extends IntegrationTestCase
{
    private CategoryService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new CategoryService(
            new CategoryRepository($this->connection),
            new PostRepository($this->connection),
        );
    }

    public function testGetHomePagePreviewsExcludesEmptyCategoriesAndLimitsPosts(): void
    {
        $phpId = $this->insertCategory('PHP', 'php');
        $this->insertCategory('Пустая', 'empty');

        for ($i = 1; $i <= 5; $i++) {
            $postId = $this->insertPost("Пост $i", "post-$i", sprintf('2026-01-%02d 10:00:00', $i));
            $this->linkPostCategory($postId, $phpId);
        }

        $previews = $this->service->getHomePagePreviews(3);

        self::assertCount(1, $previews);
        self::assertSame('php', $previews[0]->category->slug);
        self::assertCount(3, $previews[0]->posts);
        self::assertSame('post-5', $previews[0]->posts[0]->slug);
    }

    public function testGetCategoryPageThrowsWhenCategoryMissing(): void
    {
        $this->expectException(CategoryNotFoundException::class);

        $this->service->getCategoryPage('missing', PostSort::DATE, 1);
    }
}
