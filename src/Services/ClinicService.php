<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ClinicRepository;

final class ClinicService
{
    private ClinicRepository $clinics;

    public function __construct()
    {
        $this->clinics = new ClinicRepository();
    }

    public function all(): array
    {
        return $this->clinics->all();
    }

    public function exists(int $id): bool
    {
        return $this->clinics->exists($id);
    }
}
