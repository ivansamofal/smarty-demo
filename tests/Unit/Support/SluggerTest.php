<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Support\Slugger;
use PHPUnit\Framework\TestCase;

final class SluggerTest extends TestCase
{
    public function testTransliteratesCyrillic(): void
    {
        self::assertSame('privet-mir', Slugger::slugify('Привет мир'));
    }

    public function testCollapsesNonAlphanumericIntoSingleHyphen(): void
    {
        self::assertSame('php-mysql', Slugger::slugify('PHP & MySQL!!!'));
    }

    public function testTrimsLeadingAndTrailingHyphens(): void
    {
        self::assertSame('test', Slugger::slugify('   -Test-   '));
    }

    public function testFallsBackToItemForEmptyResult(): void
    {
        self::assertSame('item', Slugger::slugify('!!!'));
    }
}
