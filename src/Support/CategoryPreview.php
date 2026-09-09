<?php

declare(strict_types=1);

namespace App\Support;

use App\Entity\Category;
use App\Entity\Post;

final class CategoryPreview
{
    /**
     * @param list<Post> $posts
     */
    public function __construct(
        public readonly Category $category,
        public readonly array $posts,
    ) {
    }
}
