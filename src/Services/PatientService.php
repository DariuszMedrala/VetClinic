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

    public function clientsWithPets(): array
    {
        $grouped = [];
        foreach ($this->pets->all() as $pet) {
            $grouped[$pet->clientId][] = $pet;
        }

        $result = [];
        foreach ($this->clients->all() as $client) {
            $result[] = ['client' => $client, 'pets' => $grouped[$client->userId] ?? []];
        }

        return $result;
    }

    public function petCard(int $id): ?array
    {
        $pet = $this->pets->find($id);

        if ($pet === null) {
            return null;
        }

        return [
            'pet' => $pet,
            'vaccinations' => $this->pets->vaccinations($id),
            'history' => $this->appointments->historyForPet($id),
        ];
    }

    public function create(int $clientId, int $speciesId, string $name, ?string $breed, string $sex, ?string $birthDate, ?string $weightKg): int
    {
        return $this->pets->create($clientId, $speciesId, $name, $breed, $sex, $birthDate, $weightKg);
    }

    public function update(int $id, int $speciesId, string $name, ?string $breed, string $sex, ?string $birthDate, ?string $weightKg): bool
    {
        return $this->pets->update($id, $speciesId, $name, $breed, $sex, $birthDate, $weightKg);
    }

    public function delete(int $id): bool
    {
        return $this->pets->delete($id);
    }
}
