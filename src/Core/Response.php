<?php

declare(strict_types=1);

namespace App\Core;

final class Response
{
    private int $status = 200;
    private array $headers = [];
    private string $body = '';

    public function status(int $code): self
    {
        $this->status = $code;

        return $this;
    }

    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;

        return $this;
    }

    public function html(string $html): self
    {
        $this->header('Content-Type', 'text/html; charset=utf-8');
        $this->body = $html;

        return $this;
    }

    public function json(array $data): self
    {
        $this->header('Content-Type', 'application/json; charset=utf-8');
        $this->body = (string) json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $this;
    }

    public function redirect(string $location): self
    {
        return $this->status(302)->header('Location', $location);
    }

    public function send(): void
    {
        http_response_code($this->status);

        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }

        echo $this->body;
    }
}
