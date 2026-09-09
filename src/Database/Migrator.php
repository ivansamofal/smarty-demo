<?php

declare(strict_types=1);

namespace App\Database;

use App\Exception\DatabaseException;
use PDO;
use PDOException;

final class Migrator
{
    public function __construct(private readonly PDO $connection)
    {
    }

    public function run(string $schemaPath): void
    {
        $sql = file_get_contents($schemaPath);

        if ($sql === false) {
            throw DatabaseException::unreadableSchemaFile($schemaPath);
        }

        foreach ($this->splitStatements($sql) as $statement) {
            try {
                $this->connection->exec($statement);
            } catch (PDOException $exception) {
                throw DatabaseException::migrationFailed($statement, $exception);
            }
        }
    }

    /**
     * @return list<string>
     */
    private function splitStatements(string $sql): array
    {
        $statements = [];

        foreach (explode(';', $sql) as $statement) {
            $statement = trim($statement);

            if ($statement !== '') {
                $statements[] = $statement;
            }
        }

        return $statements;
    }
}
