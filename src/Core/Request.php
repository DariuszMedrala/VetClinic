<?php

declare(strict_types=1);

namespace App\Core;

final class Request
{
    public function __construct(
        private string $method,
        private string $path,
        private array $query,
        private array $body,
        private bool $wantsJson = false,
        private array $files = [],
    ) {
    }

    public static function capture(): self
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = rtrim(parse_url($uri, PHP_URL_PATH) ?: '/', '/') ?: '/';

        return new self($method, $path, $_GET, self::parseBody(), self::expectsJson(), $_FILES);
    }

    private static function expectsJson(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        return str_contains($accept, 'application/json')
            || strtolower($requestedWith) === 'xmlhttprequest'
            || str_contains($contentType, 'application/json');
    }

    private static function parseBody(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (str_contains($contentType, 'application/json')) {
            $decoded = json_decode(file_get_contents('php://input') ?: '', true);

            return is_array($decoded) ? $decoded : [];
        }

        return $_POST;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->body;
    }

    public function wantsJson(): bool
    {
        return $this->wantsJson;
    }

    public function file(string $key): ?array
    {
        $file = $this->files[$key] ?? null;

        return is_array($file) ? $file : null;
    }

    public function ip(): string
    {
        $forwarded = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';

        if ($forwarded !== '') {
            return trim(explode(',', $forwarded)[0]);
        }

        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    public function baseUrl(): string
    {
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? '') === '443';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        return ($secure ? 'https' : 'http') . '://' . $host;
    }
}
