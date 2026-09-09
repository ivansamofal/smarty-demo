<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;

final class PostNotFoundException extends RuntimeException
{
    public static function withSlug(string $slug): self
    {
        return new self(sprintf('Post "%s" was not found.', $slug));
    }
}
