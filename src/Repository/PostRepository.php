<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Post;
use App\Support\PaginatedResult;
use App\Support\PostSort;
use DateTimeImmutable;
use PDO;

final class PostRepository
{
    public function __construct(private readonly PDO $connection)
    {
    }

    /**
     * @param list<int> $categoryIds
     * @return array<int, list<Post>>
     */
    public function findLatestByCategories(array $categoryIds, int $limitPerCategory): array
    {
        if ($categoryIds === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));

        $statement = $this->connection->prepare(
            "SELECT * FROM (
                SELECT
                    p.id, p.title, p.slug, p.description, p.content, p.image, p.views, p.published_at,
                    pc.category_id AS category_id,
                    ROW_NUMBER() OVER (PARTITION BY pc.category_id ORDER BY p.published_at DESC) AS rn
                FROM posts p
                INNER JOIN post_category pc ON pc.post_id = p.id
                WHERE pc.category_id IN ($placeholders)
             ) ranked
             WHERE ranked.rn <= ?
             ORDER BY ranked.category_id ASC, ranked.published_at DESC",
        );

        $position = 1;

        foreach ($categoryIds as $categoryId) {
            $statement->bindValue($position++, $categoryId, PDO::PARAM_INT);
        }

        $statement->bindValue($position, $limitPerCategory, PDO::PARAM_INT);
        $statement->execute();

        $postsByCategory = [];

        foreach ($statement->fetchAll() as $row) {
            $postsByCategory[(int) $row['category_id']][] = $this->hydrate($row);
        }

        return $postsByCategory;
    }

    public function countByCategory(int $categoryId): int
    {
        $statement = $this->connection->prepare(
            'SELECT COUNT(*) FROM posts p
             INNER JOIN post_category pc ON pc.post_id = p.id
             WHERE pc.category_id = :categoryId',
        );
        $statement->execute(['categoryId' => $categoryId]);

        return (int) $statement->fetchColumn();
    }

    public function findByCategoryPaginated(int $categoryId, PostSort $sort, int $page, int $perPage): PaginatedResult
    {
        $total = $this->countByCategory($categoryId);
        $offset = ($page - 1) * $perPage;

        $statement = $this->connection->prepare(sprintf(
            'SELECT p.id, p.title, p.slug, p.description, p.content, p.image, p.views, p.published_at
             FROM posts p
             INNER JOIN post_category pc ON pc.post_id = p.id
             WHERE pc.category_id = :categoryId
             ORDER BY p.%s DESC
             LIMIT :limit OFFSET :offset',
            $sort->column(),
        ));
        $statement->bindValue('categoryId', $categoryId, PDO::PARAM_INT);
        $statement->bindValue('limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue('offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        $items = array_map($this->hydrate(...), $statement->fetchAll());

        return new PaginatedResult($items, $total, $page, $perPage);
    }

    public function findBySlug(string $slug): ?Post
    {
        $statement = $this->connection->prepare(
            'SELECT id, title, slug, description, content, image, views, published_at
             FROM posts WHERE slug = :slug LIMIT 1',
        );
        $statement->execute(['slug' => $slug]);

        $row = $statement->fetch();

        return $row === false ? null : $this->hydrate($row);
    }

    /**
     * @param list<int> $categoryIds
     * @return list<Post>
     */
    public function findSimilar(int $excludePostId, array $categoryIds, int $limit): array
    {
        if ($categoryIds === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));

        $statement = $this->connection->prepare(
            "SELECT p.id, p.title, p.slug, p.description, p.content, p.image, p.views, p.published_at
             FROM posts p
             INNER JOIN post_category pc ON pc.post_id = p.id
             WHERE pc.category_id IN ($placeholders) AND p.id != ?
             GROUP BY p.id
             ORDER BY COUNT(pc.category_id) DESC, p.published_at DESC
             LIMIT ?",
        );

        $position = 1;

        foreach ($categoryIds as $categoryId) {
            $statement->bindValue($position++, $categoryId, PDO::PARAM_INT);
        }

        $statement->bindValue($position++, $excludePostId, PDO::PARAM_INT);
        $statement->bindValue($position, $limit, PDO::PARAM_INT);
        $statement->execute();

        return array_map($this->hydrate(...), $statement->fetchAll());
    }

    public function incrementViews(int $postId): void
    {
        $statement = $this->connection->prepare('UPDATE posts SET views = views + 1 WHERE id = :id');
        $statement->bindValue('id', $postId, PDO::PARAM_INT);
        $statement->execute();
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): Post
    {
        return new Post(
            (int) $row['id'],
            (string) $row['title'],
            (string) $row['slug'],
            (string) $row['description'],
            (string) $row['content'],
            (string) $row['image'],
            (int) $row['views'],
            new DateTimeImmutable((string) $row['published_at']),
        );
    }
}
