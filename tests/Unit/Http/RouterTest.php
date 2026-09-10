<?php

declare(strict_types=1);

namespace Tests\Unit\Http;

use App\Exception\RouteNotFoundException;
use App\Http\Request;
use App\Http\Response;
use App\Http\Router;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    public function testDispatchMatchesStaticRoute(): void
    {
        $router = new Router();
        $router->get('/', static fn (array $params, Request $request): Response => new Response('home'));

        $response = $router->dispatch(new Request('/', []));

        self::assertSame('home', $response->body);
    }

    public function testDispatchExtractsNamedParameters(): void
    {
        $router = new Router();
        $router->get(
            '/category/{slug}',
            static fn (array $params, Request $request): Response => new Response($params['slug']),
        );

        $response = $router->dispatch(new Request('/category/php', []));

        self::assertSame('php', $response->body);
    }

    public function testDispatchThrowsWhenNoRouteMatches(): void
    {
        $router = new Router();
        $router->get('/', static fn (array $params, Request $request): Response => new Response('home'));

        $this->expectException(RouteNotFoundException::class);

        $router->dispatch(new Request('/missing', []));
    }
}
