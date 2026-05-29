<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\AppointmentRepository;
use App\Repositories\ClientRepository;
use App\Repositories\PetRepository;

final class PatientService
{
    private ClientRepository $clients;
    private PetRepository $pets;
    private AppointmentRepository $appointments;

    public function __construct()
    {
        $this->clients = new ClientRepository();
        $this->pets = new PetRepository();
        $this->appointments = new AppointmentRepository();
    }

    public function clientsWithPets(int $clinicId): array
    {
        $grouped = [];
        foreach ($this->pets->all($clinicId) as $pet) {
            $grouped[$pet->clientId][] = $pet;
        }

        $result = [];
        foreach ($this->clients->all($clinicId) as $client) {
            $result[] = ['client' => $client, 'pets' => $grouped[$client->userId] ?? []];
        }

        return $result;
    }

    public function overdueVaccinations(int $clinicId): array
    {
        return $this->pets->overdueVaccinations($clinicId);
    }

    public function petCard(int $id, int $clinicId): ?array
    {
        $pet = $this->pets->find($id, $clinicId);

        if ($pet === null) {
            return null;
        }

        return [
            'pet' => $pet,
            'vaccinations' => $this->pets->vaccinations($id),
            'history' => $this->appointments->historyForPet($id),
        ];
    }

    public function create(int $clientId, int $speciesId, string $name, ?string $breed, string $sex, ?string $birthDate, ?string $weightKg, ?string $photoPath): int
    {
        return $this->pets->create($clientId, $speciesId, $name, $breed, $sex, $birthDate, $weightKg, $photoPath);
    }

    public function update(int $id, int $clinicId, int $speciesId, string $name, ?string $breed, string $sex, ?string $birthDate, ?string $weightKg, ?string $photoPath): bool
    {
        return $this->pets->update($id, $clinicId, $speciesId, $name, $breed, $sex, $birthDate, $weightKg, $photoPath);
    }

    public function delete(int $id, int $clinicId): bool
    {
        return $this->pets->delete($id, $clinicId);
    }
}
