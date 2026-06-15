<?php

declare(strict_types=1);

namespace App\Core;

use Closure;

final class Router
{
    private array $routes = [];

    public function get(string $path, Closure|array $handler): Route
    {
        return $this->add('GET', $path, $handler);
    }

    public function post(string $path, Closure|array $handler): Route
    {
        return $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, Closure|array $handler): Route
    {
        $route = new Route($method, $this->compile($path), $handler);
        $this->routes[] = $route;

        return $route;
    }

    private function compile(string $path): string
    {
        $path = rtrim($path, '/') ?: '/';
        $pattern = preg_replace('#\{([a-z_]+)\}#', '(?P<$1>[^/]+)', $path);

        return '#^' . $pattern . '$#';
    }

    public function dispatch(Request $request): Response
    {
        if (!in_array($request->method(), ['GET', 'POST'], true)) {
            return ErrorPage::render(400, $request->wantsJson());
        }

        foreach ($this->routes as $route) {
            if ($route->method !== $request->method()) {
                continue;
            }

            if (preg_match($route->pattern, $request->path(), $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                foreach ($route->stack() as $middleware) {
                    $result = $middleware->handle($request);

                    if ($result instanceof Response) {
                        return $result;
                    }
                }

                return $this->run($route->handler, $request, $params);
            }
        }

        return ErrorPage::render(404, $request->wantsJson());
    }

    private function run(Closure|array $handler, Request $request, array $params): Response
    {
        if (is_array($handler)) {
            [$class, $method] = $handler;

            return (new $class())->$method($request, $params);
        }

        return $handler($request, $params);
    }
}
