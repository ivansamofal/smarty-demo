#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Database\Connection;
use App\Database\Migrator;
use App\Exception\DatabaseException;

require dirname(__DIR__) . '/vendor/autoload.php';

$schemaPath = dirname(__DIR__) . '/database/schema.sql';

try {
    $migrator = new Migrator(Connection::get());
    $migrator->run($schemaPath);
} catch (DatabaseException $exception) {
    fwrite(STDERR, $exception->getMessage() . PHP_EOL);

    exit(1);
}

fwrite(STDOUT, 'Database schema is up to date.' . PHP_EOL);
