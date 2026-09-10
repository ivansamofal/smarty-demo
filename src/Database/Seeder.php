<?php

declare(strict_types=1);

namespace App\Database;

use App\Support\Slugger;
use PDO;

final class Seeder
{
    private const IMAGES_COUNT = 5;

    private const CATEGORIES = [
        ['name' => 'Веб-разработка', 'description' => 'Статьи о создании сайтов и веб-приложений.'],
        ['name' => 'Мобильная разработка', 'description' => 'Материалы про Android, iOS и кроссплатформенные приложения.'],
        ['name' => 'Базы данных', 'description' => 'Проектирование схем, оптимизация запросов, администрирование СУБД.'],
        ['name' => 'DevOps', 'description' => 'Docker, CI/CD, мониторинг и эксплуатация серверов.'],
        ['name' => 'Тестирование', 'description' => 'Юнит-тесты, автоматизация и обеспечение качества кода.'],
        ['name' => 'Карьера в IT', 'description' => 'Советы по трудоустройству и профессиональному росту.'],
        ['name' => 'Черновики', 'description' => 'Категория, для которой ещё не написано ни одной статьи.'],
    ];

    private const TITLE_TEMPLATES = [
        'Введение в %s',
        'Как использовать %s в реальных проектах',
        '%s: подробное руководство',
        'Пять ошибок при работе с %s',
        'Сравнение подходов в %s',
        'Практический опыт: %s',
        'Что нового в %s в этом году',
        'Почему стоит изучать %s',
    ];

    private const TOPICS = [
        'PHP', 'MySQL', 'Docker', 'Smarty', 'REST API', 'Git', 'Redis',
        'Nginx', 'Kubernetes', 'PostgreSQL', 'JavaScript', 'TypeScript',
        'React', 'Vue', 'CI/CD', 'Linux', 'Bash', 'Composer', 'GraphQL', 'WebSocket',
    ];

    private const SENTENCES = [
        'Это позволяет разработчикам быстрее находить нужное решение.',
        'На практике такой подход экономит время команды.',
        'Важно учитывать особенности конкретного проекта.',
        'Многие разработчики сталкиваются с этой проблемой на старте.',
        'Документация по теме постоянно обновляется.',
        'Стоит начать с простого примера и постепенно усложнять его.',
        'Такой инструмент хорошо подходит для небольших команд.',
        'Производительность заметно возрастает после оптимизации.',
        'Тестирование помогает выявить ошибки на раннем этапе.',
        'Сообщество активно развивает эту технологию.',
        'Каждый случай требует индивидуального подхода.',
        'Автоматизация рутинных задач освобождает время для разработки.',
        'Читаемый код проще поддерживать в долгосрочной перспективе.',
        'Опыт других команд помогает избежать типичных ошибок.',
        'Регулярный рефакторинг снижает технический долг.',
    ];

    public function __construct(private readonly PDO $connection)
    {
    }

    public function run(int $postCount = 30): void
    {
        $categoryIds = $this->seedCategories();
        $this->seedPosts($postCount, array_slice($categoryIds, 0, -1));
    }

    /**
     * @return list<int>
     */
    private function seedCategories(): array
    {
        $statement = $this->connection->prepare(
            'INSERT INTO categories (name, slug, description) VALUES (:name, :slug, :description)',
        );

        $ids = [];

        foreach (self::CATEGORIES as $category) {
            $statement->execute([
                'name' => $category['name'],
                'slug' => Slugger::slugify($category['name']),
                'description' => $category['description'],
            ]);

            $ids[] = (int) $this->connection->lastInsertId();
        }

        return $ids;
    }

    /**
     * @param list<int> $categoryIds
     */
    private function seedPosts(int $count, array $categoryIds): void
    {
        $postStatement = $this->connection->prepare(
            'INSERT INTO posts (title, slug, description, content, image, views, published_at)
             VALUES (:title, :slug, :description, :content, :image, :views, :publishedAt)',
        );

        $linkStatement = $this->connection->prepare(
            'INSERT INTO post_category (post_id, category_id) VALUES (:postId, :categoryId)',
        );

        for ($i = 1; $i <= $count; $i++) {
            $topic = self::TOPICS[array_rand(self::TOPICS)];
            $title = sprintf(self::TITLE_TEMPLATES[array_rand(self::TITLE_TEMPLATES)], $topic);

            $postStatement->execute([
                'title' => $title,
                'slug' => Slugger::slugify($title) . '-' . $i,
                'description' => $this->randomSentences(2),
                'content' => $this->randomContent(),
                'image' => sprintf('/images/placeholders/placeholder-%d.svg', random_int(1, self::IMAGES_COUNT)),
                'views' => random_int(0, 500),
                'publishedAt' => $this->randomPublishedAt(),
            ]);

            $postId = (int) $this->connection->lastInsertId();

            foreach ($this->randomCategoryIds($categoryIds) as $categoryId) {
                $linkStatement->execute(['postId' => $postId, 'categoryId' => $categoryId]);
            }
        }
    }

    /**
     * @param list<int> $categoryIds
     * @return list<int>
     */
    private function randomCategoryIds(array $categoryIds): array
    {
        $keys = (array) array_rand($categoryIds, random_int(1, min(3, count($categoryIds))));

        return array_map(static fn (int $key): int => $categoryIds[$key], $keys);
    }

    private function randomSentences(int $count): string
    {
        $keys = (array) array_rand(self::SENTENCES, $count);

        return implode(' ', array_map(static fn (int $key): string => self::SENTENCES[$key], $keys));
    }

    private function randomContent(): string
    {
        $paragraphs = [];

        for ($i = 0; $i < random_int(3, 5); $i++) {
            $paragraphs[] = $this->randomSentences(random_int(3, 5));
        }

        return implode("\n\n", $paragraphs);
    }

    private function randomPublishedAt(): string
    {
        return date('Y-m-d H:i:s', strtotime(sprintf('-%d days', random_int(0, 180))));
    }
}
