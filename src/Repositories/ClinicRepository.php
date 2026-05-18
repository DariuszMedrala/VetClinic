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

    public function exists(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM clinics WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return (bool) $stmt->fetchColumn();
    }

    public function create(string $name, string $address): int
    {
        $stmt = $this->db->prepare('INSERT INTO clinics (name, address) VALUES (:name, :address) RETURNING id');
        $stmt->execute(['name' => $name, 'address' => $address]);

        return (int) $stmt->fetchColumn();
    }
}
