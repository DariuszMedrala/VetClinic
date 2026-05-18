<?php

declare(strict_types=1);

namespace App\Services;

use finfo;

final class PhotoUploadService
{
    private const MAX_BYTES = 5 * 1024 * 1024;
    private const TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    public function store(?array $file): array
    {
        if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return ['ok' => true, 'path' => null];
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
            return ['ok' => false, 'error' => 'Nie udało się wgrać pliku.'];
        }

        if ((int) $file['size'] > self::MAX_BYTES) {
            return ['ok' => false, 'error' => 'Zdjęcie może mieć maksymalnie 5 MB.'];
        }

        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);

        if (!isset(self::TYPES[$mime])) {
            return ['ok' => false, 'error' => 'Dozwolone formaty: JPG, PNG, WebP, GIF.'];
        }

        $name = 'pet_' . bin2hex(random_bytes(8)) . '.' . self::TYPES[$mime];
        $directory = APP_ROOT . '/public/assets/uploads/pets';

        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            return ['ok' => false, 'error' => 'Nie udało się zapisać zdjęcia.'];
        }

        if (!move_uploaded_file($file['tmp_name'], $directory . '/' . $name)) {
            return ['ok' => false, 'error' => 'Nie udało się zapisać zdjęcia.'];
        }

        return ['ok' => true, 'path' => '/assets/uploads/pets/' . $name];
    }
}
