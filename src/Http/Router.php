<?php

declare(strict_types=1);

namespace App\Http;

use App\Exception\RouteNotFoundException;

final class Router
{
    /** @var list<array{regex: string, handler: callable}> */
    private array $routes = [];

    public function get(string $pattern, callable $handler): void
    {
        $this->routes[] = [
            'regex' => $this->compile($pattern),
            'handler' => $handler,
        ];
    }

    public function dispatch(Request $request): Response
    {
        foreach ($this->routes as $route) {
            if (preg_match($route['regex'], $request->path(), $matches) === 1) {
                $params = array_filter($matches, static fn (int|string $key): bool => is_string($key), ARRAY_FILTER_USE_KEY);

                return ($route['handler'])($params, $request);
            }
        }

        throw RouteNotFoundException::forPath($request->path());
    }

    private function compile(string $pattern): string
    {
        $regex = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $pattern);

        return '#^' . $regex . '$#';
    }
}
