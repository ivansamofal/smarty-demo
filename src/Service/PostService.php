<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Category;
use App\Exception\PostNotFoundException;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Support\PostPage;

final class PostService
{
    private const SIMILAR_POSTS_LIMIT = 3;

    public function __construct(
        private readonly PostRepository $posts,
        private readonly CategoryRepository $categories,
    ) {
    }

    public function getPostPage(string $slug): PostPage
    {
        $post = $this->posts->findBySlug($slug);

        if ($post === null) {
            throw PostNotFoundException::withSlug($slug);
        }

        $this->posts->incrementViews($post->id);
        $post = $post->withViews($post->views + 1);

        $categories = $this->categories->findByPostId($post->id);
        $categoryIds = array_map(static fn (Category $category): int => $category->id, $categories);

        $similarPosts = $this->posts->findSimilar($post->id, $categoryIds, self::SIMILAR_POSTS_LIMIT);

        return new PostPage($post, $categories, $similarPosts);
    }
}
