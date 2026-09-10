<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Support\PostSort;
use PHPUnit\Framework\TestCase;

final class PostSortTest extends TestCase
{
    public function testTryFromValidValues(): void
    {
        self::assertSame(PostSort::DATE, PostSort::tryFrom('date'));
        self::assertSame(PostSort::VIEWS, PostSort::tryFrom('views'));
    }

    public function testTryFromInvalidValueReturnsNull(): void
    {
        self::assertNull(PostSort::tryFrom('popularity'));
    }

    public function testColumnMapsToDatabaseColumn(): void
    {
        self::assertSame('published_at', PostSort::DATE->column());
        self::assertSame('views', PostSort::VIEWS->column());
    }
}
