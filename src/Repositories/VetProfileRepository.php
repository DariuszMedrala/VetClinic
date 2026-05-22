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

    public function find(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT title, room, specialization, license_number FROM vet_profiles WHERE user_id = :id'
        );
        $stmt->execute(['id' => $userId]);
        $row = $stmt->fetch();

        return $row ?: ['title' => '', 'room' => '', 'specialization' => '', 'license_number' => ''];
    }

    public function update(int $userId, string $title, ?string $room, ?string $specialization): void
    {
        $stmt = $this->db->prepare(
            'UPDATE vet_profiles SET title = :title, room = :room, specialization = :spec WHERE user_id = :id'
        );
        $stmt->execute(['title' => $title, 'room' => $room, 'spec' => $specialization, 'id' => $userId]);
    }
}
