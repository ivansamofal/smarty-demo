<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;

final class CategoryNotFoundException extends RuntimeException
{
    public static function withSlug(string $slug): self
    {
        return new self(sprintf('Category "%s" was not found.', $slug));
    }
}
