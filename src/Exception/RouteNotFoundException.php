<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;

final class RouteNotFoundException extends RuntimeException
{
    public static function forPath(string $path): self
    {
        return new self(sprintf('No route matches path "%s".', $path));
    }
}
