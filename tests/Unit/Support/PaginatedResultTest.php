<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Support\PaginatedResult;
use PHPUnit\Framework\TestCase;

final class PaginatedResultTest extends TestCase
{
    public function testTotalPagesRoundsUp(): void
    {
        $result = new PaginatedResult([], 21, 1, 10);

        self::assertSame(3, $result->totalPages());
    }

    public function testTotalPagesIsZeroWhenPerPageIsZero(): void
    {
        $result = new PaginatedResult([], 21, 1, 0);

        self::assertSame(0, $result->totalPages());
    }

    public function testHasPreviousAndHasNextBoundaries(): void
    {
        $first = new PaginatedResult([], 30, 1, 10);
        $middle = new PaginatedResult([], 30, 2, 10);
        $last = new PaginatedResult([], 30, 3, 10);

        self::assertFalse($first->hasPrevious());
        self::assertTrue($first->hasNext());

        self::assertTrue($middle->hasPrevious());
        self::assertTrue($middle->hasNext());

        self::assertTrue($last->hasPrevious());
        self::assertFalse($last->hasNext());
    }

    public function testPreviousPageNeverGoesBelowOne(): void
    {
        $result = new PaginatedResult([], 30, 1, 10);

        self::assertSame(1, $result->previousPage());
    }

    public function testNextPageIncrementsCurrentPage(): void
    {
        $result = new PaginatedResult([], 30, 2, 10);

        self::assertSame(3, $result->nextPage());
    }
}
