<?php

declare(strict_types=1);

namespace App\Support;

enum PostSort: string
{
    case DATE = 'date';
    case VIEWS = 'views';

    public function column(): string
    {
        return match ($this) {
            self::DATE => 'published_at',
            self::VIEWS => 'views',
        };
    }
}
