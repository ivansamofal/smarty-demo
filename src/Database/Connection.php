<?php

declare(strict_types=1);

namespace App\Database;

use App\Config\Config;
use App\Exception\DatabaseException;
use PDO;
use PDOException;

final class Connection
{
    private static ?PDO $instance = null;

    public static function get(): PDO
    {
        if (self::$instance === null) {
            self::$instance = self::createConnection();
        }

        return self::$instance;
    }

    private static function createConnection(): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            Config::require('DB_HOST'),
            Config::require('DB_PORT'),
            Config::require('DB_DATABASE'),
        );

        try {
            return new PDO(
                $dsn,
                Config::require('DB_USERNAME'),
                Config::require('DB_PASSWORD'),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ],
            );
        } catch (PDOException $exception) {
            throw DatabaseException::connectionFailed($exception);
        }
    }
}
