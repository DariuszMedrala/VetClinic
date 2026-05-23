<?php

declare(strict_types=1);

if (!function_exists('e')) {
    function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        $file = APP_ROOT . '/public' . $path;

        if (is_file($file)) {
            return $path . '?v=' . filemtime($file);
        }

        return $path;
    }
}
