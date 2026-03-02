<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class View
{
    private string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/') . '/';
    }

    public function render(string $template, array $data = [], ?string $layout = null): string
    {
        $content = $this->renderFile($template, $data);

        if ($layout !== null) {
            return $this->renderFile('layouts/' . $layout, array_merge($data, ['content' => $content]));
        }

        return $content;
    }

    private function renderFile(string $template, array $data): string
    {
        $file = $this->basePath . $template . '.php';

        if (!is_file($file)) {
            throw new RuntimeException('Nie znaleziono widoku: ' . $template);
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $file;

        return (string) ob_get_clean();
    }
}
