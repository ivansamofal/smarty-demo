<?php

declare(strict_types=1);

namespace Tests\Unit\Exception;

use App\Exception\CategoryNotFoundException;
use App\Exception\ConfigurationException;
use App\Exception\PostNotFoundException;
use App\Exception\RouteNotFoundException;
use PHPUnit\Framework\TestCase;

final class DomainExceptionsTest extends TestCase
{
    public function testCategoryNotFoundMessage(): void
    {
        self::assertSame('Category "php" was not found.', CategoryNotFoundException::withSlug('php')->getMessage());
    }

    public function testPostNotFoundMessage(): void
    {
        self::assertSame('Post "my-post" was not found.', PostNotFoundException::withSlug('my-post')->getMessage());
    }

    public function testRouteNotFoundMessage(): void
    {
        self::assertSame('No route matches path "/missing".', RouteNotFoundException::forPath('/missing')->getMessage());
    }

    public function testConfigurationMissingKeyMessage(): void
    {
        self::assertSame(
            'Missing required configuration value "DB_HOST".',
            ConfigurationException::missingKey('DB_HOST')->getMessage(),
        );
    }
}
