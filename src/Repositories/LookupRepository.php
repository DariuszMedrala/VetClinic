<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class LookupRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function vets(int $clinicId): array
    {
        $stmt = $this->db->prepare(
            "SELECT vp.user_id AS id,
                    vp.title || ' ' || u.first_name || ' ' || u.last_name AS name
             FROM vet_profiles vp
             JOIN users u ON u.id = vp.user_id
             WHERE u.is_active = TRUE AND u.clinic_id = :c
             ORDER BY name"
        );
        $stmt->execute(['c' => $clinicId]);

        return $stmt->fetchAll();
    }

    public function species(): array
    {
        return $this->db->query('SELECT id, name FROM species ORDER BY name')->fetchAll();
    }

    public function pets(int $clinicId): array
    {
        $stmt = $this->db->prepare(
            "SELECT p.id,
                    p.name || ' (' || s.name || ') — ' || cu.first_name || ' ' || cu.last_name AS label
             FROM pets p
             JOIN species s ON s.id = p.species_id
             JOIN clients c ON c.user_id = p.client_id
             JOIN users cu ON cu.id = c.user_id
             WHERE cu.clinic_id = :c
             ORDER BY p.name"
        );
        $stmt->execute(['c' => $clinicId]);

        return $stmt->fetchAll();
    }

    public function vetInClinic(int $vetId, int $clinicId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM vet_profiles vp JOIN users u ON u.id = vp.user_id
             WHERE vp.user_id = :vet AND u.clinic_id = :c'
        );
        $stmt->execute(['vet' => $vetId, 'c' => $clinicId]);

        return (bool) $stmt->fetchColumn();
    }

    public function petInClinic(int $petId, int $clinicId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM pets p JOIN clients c ON c.user_id = p.client_id JOIN users u ON u.id = c.user_id
             WHERE p.id = :pet AND u.clinic_id = :c'
        );
        $stmt->execute(['pet' => $petId, 'c' => $clinicId]);

        return (bool) $stmt->fetchColumn();
    }

    public function clientInClinic(int $clientId, int $clinicId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM clients c JOIN users u ON u.id = c.user_id
             WHERE c.user_id = :cl AND u.clinic_id = :c'
        );
        $stmt->execute(['cl' => $clientId, 'c' => $clinicId]);

        return (bool) $stmt->fetchColumn();
    }
}
