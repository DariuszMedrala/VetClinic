<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class ClinicRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function all(): array
    {
        return $this->db->query('SELECT id, name, address FROM clinics ORDER BY name')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, name, address, join_code FROM clinics WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function exists(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM clinics WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return (bool) $stmt->fetchColumn();
    }

    public function verifyJoinCode(int $id, string $joinCode): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM clinics WHERE id = :id AND join_code = :code');
        $stmt->execute(['id' => $id, 'code' => $joinCode]);

        return (bool) $stmt->fetchColumn();
    }

    public function nameExistsForOther(string $name, int $id): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM clinics WHERE name = :name AND id <> :id');
        $stmt->execute(['name' => $name, 'id' => $id]);

        return (bool) $stmt->fetchColumn();
    }

    public function create(string $name, string $address, string $joinCode): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO clinics (name, address, join_code) VALUES (:name, :address, :code) RETURNING id'
        );
        $stmt->execute(['name' => $name, 'address' => $address, 'code' => $joinCode]);

        return (int) $stmt->fetchColumn();
    }

    public function update(int $id, string $name, string $address, string $joinCode): void
    {
        $stmt = $this->db->prepare(
            'UPDATE clinics SET name = :name, address = :address, join_code = :code WHERE id = :id'
        );
        $stmt->execute(['name' => $name, 'address' => $address, 'code' => $joinCode, 'id' => $id]);
    }
}
