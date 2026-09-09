<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use PDO;

final class CategoryRepository
{
    public function __construct(private readonly PDO $connection)
    {
    }

    /**
     * @return list<Category>
     */
    public function findAllWithPosts(): array
    {
        $statement = $this->connection->query(
            'SELECT DISTINCT c.id, c.name, c.slug, c.description
             FROM categories c
             INNER JOIN post_category pc ON pc.category_id = c.id
             ORDER BY c.name ASC',
        );

        return array_map($this->hydrate(...), $statement->fetchAll());
    }

    public function findBySlug(string $slug): ?Category
    {
        $statement = $this->connection->prepare(
            'SELECT id, name, slug, description FROM categories WHERE slug = :slug LIMIT 1',
        );
        $statement->execute(['slug' => $slug]);

        $row = $statement->fetch();

        return $row === false ? null : $this->hydrate($row);
    }

    /**
     * @return list<Category>
     */
    public function findByPostId(int $postId): array
    {
        $statement = $this->connection->prepare(
            'SELECT c.id, c.name, c.slug, c.description
             FROM categories c
             INNER JOIN post_category pc ON pc.category_id = c.id
             WHERE pc.post_id = :postId
             ORDER BY c.name ASC',
        );
        $statement->execute(['postId' => $postId]);

        return array_map($this->hydrate(...), $statement->fetchAll());
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): Category
    {
        return new Category(
            (int) $row['id'],
            (string) $row['name'],
            (string) $row['slug'],
            (string) $row['description'],
        );
    }
}
