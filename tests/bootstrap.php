<?php

declare(strict_types=1);

use App\Config\Config;
use App\Database\Connection;
use App\Database\Migrator;

require dirname(__DIR__) . '/vendor/autoload.php';

try {
    $database = Config::require('DB_DATABASE');
    $appUser = Config::require('DB_USERNAME');

    $root = new PDO(
        sprintf('mysql:host=%s;port=%s;charset=utf8mb4', Config::require('DB_HOST'), Config::require('DB_PORT')),
        'root',
        Config::require('DB_ROOT_PASSWORD'),
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
    );
    $root->exec(sprintf(
        'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
        $database,
    ));
    $root->exec(sprintf("GRANT ALL PRIVILEGES ON `%s`.* TO '%s'@'%%'", $database, $appUser));
    $root->exec('FLUSH PRIVILEGES');

    (new Migrator(Connection::get()))->run(dirname(__DIR__) . '/database/schema.sql');
} catch (Throwable) {
}
