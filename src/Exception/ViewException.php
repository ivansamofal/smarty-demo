<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;

final class ViewException extends RuntimeException
{
    public static function directoryNotCreatable(string $path): self
    {
        return new self(sprintf('Unable to create directory "%s".', $path));
    }

    public static function directoryNotWritable(string $path): self
    {
        return new self(sprintf('Directory "%s" is not writable.', $path));
    }
}
