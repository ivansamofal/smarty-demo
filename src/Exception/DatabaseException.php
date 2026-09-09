<?php

declare(strict_types=1);

namespace App\Exception;

use PDOException;
use RuntimeException;

final class DatabaseException extends RuntimeException
{
    public static function connectionFailed(PDOException $previous): self
    {
        return new self('Unable to connect to the database.', 0, $previous);
    }

    public static function migrationFailed(string $statement, PDOException $previous): self
    {
        return new self(sprintf('Migration statement failed: %s', $statement), 0, $previous);
    }

    public static function unreadableSchemaFile(string $path): self
    {
        return new self(sprintf('Unable to read schema file "%s".', $path));
    }
}
