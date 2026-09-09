<?php

declare(strict_types=1);

namespace App\Support;

use App\Entity\Category;

final class CategoryPage
{
    public function __construct(
        public readonly Category $category,
        public readonly PaginatedResult $posts,
        public readonly PostSort $sort,
    ) {
    }
}
