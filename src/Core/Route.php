<?php

declare(strict_types=1);

namespace App\Core;

use Closure;

final class Route
{
    private array $middleware = [];

    public function __construct(
        public readonly string $method,
        public readonly string $pattern,
        public readonly Closure|array $handler,
    ) {
    }

    public function middleware(Middleware ...$middleware): self
    {
        $this->middleware = array_merge($this->middleware, $middleware);

        return $this;
    }

    public function stack(): array
    {
        return $this->middleware;
    }
}
