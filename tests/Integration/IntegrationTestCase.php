<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Database\Connection;
use App\Exception\DatabaseException;
use PDO;
use PHPUnit\Framework\TestCase;

abstract class IntegrationTestCase extends TestCase
{
    protected PDO $connection;

    protected function setUp(): void
    {
        try {
            $this->connection = Connection::get();
        } catch (DatabaseException $exception) {
            self::markTestSkipped('Test database is not reachable: ' . $exception->getMessage());
        }

        $this->connection->exec('SET FOREIGN_KEY_CHECKS=0');
        $this->connection->exec('TRUNCATE TABLE post_category');
        $this->connection->exec('TRUNCATE TABLE posts');
        $this->connection->exec('TRUNCATE TABLE categories');
        $this->connection->exec('SET FOREIGN_KEY_CHECKS=1');
    }

    protected function insertCategory(string $name, string $slug, string $description = ''): int
    {
        $statement = $this->connection->prepare(
            'INSERT INTO categories (name, slug, description) VALUES (:name, :slug, :description)',
        );
        $statement->execute(['name' => $name, 'slug' => $slug, 'description' => $description]);

        return (int) $this->connection->lastInsertId();
    }

    protected function insertPost(
        string $title,
        string $slug,
        string $publishedAt,
        int $views = 0,
        string $description = '',
        string $content = '',
        string $image = '/images/placeholders/placeholder-1.svg',
    ): int {
        $statement = $this->connection->prepare(
            'INSERT INTO posts (title, slug, description, content, image, views, published_at)
             VALUES (:title, :slug, :description, :content, :image, :views, :publishedAt)',
        );
        $statement->execute([
            'title' => $title,
            'slug' => $slug,
            'description' => $description,
            'content' => $content,
            'image' => $image,
            'views' => $views,
            'publishedAt' => $publishedAt,
        ]);

        return (int) $this->connection->lastInsertId();
    }

    protected function linkPostCategory(int $postId, int $categoryId): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO post_category (post_id, category_id) VALUES (:postId, :categoryId)',
        );
        $statement->execute(['postId' => $postId, 'categoryId' => $categoryId]);
    }
}
