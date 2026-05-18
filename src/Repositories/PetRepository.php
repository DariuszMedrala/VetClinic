<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\Pet;
use PDO;

final class PetRepository
{
    private const COLUMNS = 'p.id, p.client_id, p.species_id, s.name AS species,
                p.name, p.breed, p.sex, p.birth_date, p.weight_kg,
                cu.first_name || \' \' || cu.last_name AS owner_name,
                c.phone AS owner_phone, c.loyalty_points';

    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function all(int $clinicId): array
    {
        $stmt = $this->db->prepare(
            'SELECT ' . self::COLUMNS . '
             FROM pets p
             JOIN species s ON s.id = p.species_id
             JOIN clients c ON c.user_id = p.client_id
             JOIN users cu ON cu.id = c.user_id
             WHERE cu.clinic_id = :c
             ORDER BY cu.last_name, p.name'
        );
        $stmt->execute(['c' => $clinicId]);

        return array_map(
            static fn (array $row): Pet => Pet::fromRow($row),
            $stmt->fetchAll()
        );
    }

    public function forClient(int $clientId): array
    {
        $stmt = $this->db->prepare(
            'SELECT ' . self::COLUMNS . '
             FROM pets p
             JOIN species s ON s.id = p.species_id
             JOIN clients c ON c.user_id = p.client_id
             JOIN users cu ON cu.id = c.user_id
             WHERE p.client_id = :id
             ORDER BY p.name'
        );
        $stmt->execute(['id' => $clientId]);

        return array_map(
            static fn (array $row): Pet => Pet::fromRow($row),
            $stmt->fetchAll()
        );
    }

    public function find(int $id, int $clinicId): ?Pet
    {
        $stmt = $this->db->prepare(
            'SELECT ' . self::COLUMNS . '
             FROM pets p
             JOIN species s ON s.id = p.species_id
             JOIN clients c ON c.user_id = p.client_id
             JOIN users cu ON cu.id = c.user_id
             WHERE p.id = :id AND cu.clinic_id = :c'
        );
        $stmt->execute(['id' => $id, 'c' => $clinicId]);
        $row = $stmt->fetch();

        return $row ? Pet::fromRow($row) : null;
    }

    public function vaccinations(int $petId): array
    {
        $stmt = $this->db->prepare(
            'SELECT vaccine_name, administered_at, expires_at, status, administered_by
             FROM vw_pet_vaccination_status
             WHERE pet_id = :id
             ORDER BY administered_at DESC'
        );
        $stmt->execute(['id' => $petId]);

        return $stmt->fetchAll();
    }

    public function create(int $clientId, int $speciesId, string $name, ?string $breed, string $sex, ?string $birthDate, ?string $weightKg): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO pets (client_id, species_id, name, breed, sex, birth_date, weight_kg)
             VALUES (:client, :species, :name, :breed, CAST(:sex AS animal_sex), :birth, :weight)
             RETURNING id'
        );
        $stmt->execute([
            'client' => $clientId,
            'species' => $speciesId,
            'name' => $name,
            'breed' => $breed,
            'sex' => $sex,
            'birth' => $birthDate,
            'weight' => $weightKg,
        ]);

        return (int) $stmt->fetchColumn();
    }

    public function update(int $id, int $clinicId, int $speciesId, string $name, ?string $breed, string $sex, ?string $birthDate, ?string $weightKg): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE pets
             SET species_id = :species, name = :name, breed = :breed,
                 sex = CAST(:sex AS animal_sex), birth_date = :birth, weight_kg = :weight
             WHERE id = :id AND client_id IN (
                 SELECT c.user_id FROM clients c JOIN users u ON u.id = c.user_id WHERE u.clinic_id = :c
             )'
        );
        $stmt->execute([
            'id' => $id,
            'c' => $clinicId,
            'species' => $speciesId,
            'name' => $name,
            'breed' => $breed,
            'sex' => $sex,
            'birth' => $birthDate,
            'weight' => $weightKg,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function delete(int $id, int $clinicId): bool
    {
        $stmt = $this->db->prepare(
            'DELETE FROM pets
             WHERE id = :id AND client_id IN (
                 SELECT c.user_id FROM clients c JOIN users u ON u.id = c.user_id WHERE u.clinic_id = :c
             )'
        );
        $stmt->execute(['id' => $id, 'c' => $clinicId]);

        return $stmt->rowCount() > 0;
    }
}
