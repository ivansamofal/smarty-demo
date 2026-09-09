<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;

final class Post
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $slug,
        public readonly string $description,
        public readonly string $content,
        public readonly string $image,
        public readonly int $views,
        public readonly DateTimeImmutable $publishedAt,
    ) {
    }

    public function withViews(int $views): self
    {
        return new self(
            $this->id,
            $this->title,
            $this->slug,
            $this->description,
            $this->content,
            $this->image,
            $views,
            $this->publishedAt,
        );
    }
}
