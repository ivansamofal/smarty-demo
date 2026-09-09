<?php

declare(strict_types=1);

namespace App\Support;

use App\Entity\Category;
use App\Entity\Post;

final class PostPage
{
    /**
     * @param list<Category> $categories
     * @param list<Post> $similarPosts
     */
    public function __construct(
        public readonly Post $post,
        public readonly array $categories,
        public readonly array $similarPosts,
    ) {
    }
}
