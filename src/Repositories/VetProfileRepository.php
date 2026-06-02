<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class VetProfileRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function listForClinic(int $clinicId): array
    {
        $stmt = $this->db->prepare(
            "SELECT u.id, u.first_name, u.last_name, u.email,
                    vp.title, vp.room, vp.specialization, vp.license_number
             FROM vet_profiles vp
             JOIN users u ON u.id = vp.user_id
             WHERE u.clinic_id = :c AND u.is_active = TRUE
             ORDER BY u.last_name, u.first_name"
        );
        $stmt->execute(['c' => $clinicId]);

        return $stmt->fetchAll();
    }

    public function find(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT title, room, specialization, license_number FROM vet_profiles WHERE user_id = :id'
        );
        $stmt->execute(['id' => $userId]);
        $row = $stmt->fetch();

        return $row ?: ['title' => '', 'room' => '', 'specialization' => '', 'license_number' => ''];
    }

    public function update(int $userId, string $title, ?string $room, ?string $specialization, string $licenseNumber): void
    {
        $stmt = $this->db->prepare(
            'UPDATE vet_profiles SET title = :title, room = :room, specialization = :spec, license_number = :license WHERE user_id = :id'
        );
        $stmt->execute([
            'title' => $title,
            'room' => $room,
            'spec' => $specialization,
            'license' => $licenseNumber,
            'id' => $userId,
        ]);
    }

    public function licenseExistsForOther(string $licenseNumber, int $userId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM vet_profiles WHERE license_number = :license AND user_id <> :id'
        );
        $stmt->execute(['license' => $licenseNumber, 'id' => $userId]);

        return $stmt->fetchColumn() !== false;
    }
}
