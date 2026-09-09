<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;

final class ConfigurationException extends RuntimeException
{
    public static function missingKey(string $key): self
    {
        return new self(sprintf('Missing required configuration value "%s".', $key));
    }

    public static function unreadableFile(string $path): self
    {
        return new self(sprintf('Unable to read configuration file "%s".', $path));
    }
}
