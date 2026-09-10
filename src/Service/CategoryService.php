<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Category;
use App\Exception\CategoryNotFoundException;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Support\CategoryPage;
use App\Support\CategoryPreview;
use App\Support\PostSort;

final class CategoryService
{
    public function __construct(
        private readonly CategoryRepository $categories,
        private readonly PostRepository $posts,
    ) {
    }

    /**
     * @return list<CategoryPreview>
     */
    public function getHomePagePreviews(int $postsPerCategory = 3): array
    {
        $categories = $this->categories->findAllWithPosts();

        if ($categories === []) {
            return [];
        }

        $categoryIds = array_map(static fn (Category $category): int => $category->id, $categories);
        $postsByCategory = $this->posts->findLatestByCategories($categoryIds, $postsPerCategory);

        $previews = [];

        foreach ($categories as $category) {
            $previews[] = new CategoryPreview($category, $postsByCategory[$category->id] ?? []);
        }

        return $previews;
    }

    public function getCategoryPage(string $slug, PostSort $sort, int $page, int $perPage = 10): CategoryPage
    {
        $category = $this->categories->findBySlug($slug);

        if ($category === null) {
            throw CategoryNotFoundException::withSlug($slug);
        }

        $paginated = $this->posts->findByCategoryPaginated($category->id, $sort, max($page, 1), $perPage);

        return new CategoryPage($category, $paginated, $sort);
    }
}
