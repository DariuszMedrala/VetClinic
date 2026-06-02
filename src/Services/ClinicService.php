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

    public function find(int $id): ?array
    {
        return $this->clinics->find($id);
    }

    public function exists(int $id): bool
    {
        return $this->clinics->exists($id);
    }

    public function verifyJoinCode(int $id, string $joinCode): bool
    {
        return $this->clinics->verifyJoinCode($id, $joinCode);
    }

    public function update(int $id, string $name, string $address, string $joinCode): array
    {
        if ($name === '' || $address === '') {
            return ['ok' => false, 'message' => 'Nazwa i adres kliniki są wymagane.'];
        }

        if ($joinCode === '') {
            return ['ok' => false, 'message' => 'Hasło dołączeniowe jest wymagane.'];
        }

        if ($this->clinics->nameExistsForOther($name, $id)) {
            return ['ok' => false, 'message' => 'Klinika o tej nazwie już istnieje.'];
        }

        $this->clinics->update($id, $name, $address, $joinCode);

        return ['ok' => true, 'message' => 'Dane kliniki zostały zapisane.'];
    }
}
