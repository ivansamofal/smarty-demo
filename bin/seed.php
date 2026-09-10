#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Database\Connection;
use App\Database\Seeder;

require dirname(__DIR__) . '/vendor/autoload.php';

$options = getopt('', ['fresh', 'count::']);
$postCount = isset($options['count']) ? (int) $options['count'] : 30;

try {
    $connection = Connection::get();

    if (array_key_exists('fresh', $options)) {
        $connection->exec('SET FOREIGN_KEY_CHECKS=0');
        $connection->exec('TRUNCATE TABLE post_category');
        $connection->exec('TRUNCATE TABLE posts');
        $connection->exec('TRUNCATE TABLE categories');
        $connection->exec('SET FOREIGN_KEY_CHECKS=1');
    }

    (new Seeder($connection))->run($postCount);
} catch (Throwable $exception) {
    fwrite(STDERR, $exception->getMessage() . PHP_EOL);

    exit(1);
}

fwrite(STDOUT, sprintf('Seeded categories and %d posts.', $postCount) . PHP_EOL);
