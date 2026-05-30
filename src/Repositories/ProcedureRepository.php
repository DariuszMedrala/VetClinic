<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class ProcedureRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function all(int $clinicId): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, description, base_price FROM procedures
             WHERE clinic_id = :c AND is_active = TRUE ORDER BY name'
        );
        $stmt->execute(['c' => $clinicId]);

        return $stmt->fetchAll();
    }

    public function priceMap(int $clinicId): array
    {
        $map = [];
        foreach ($this->all($clinicId) as $row) {
            $map[(int) $row['id']] = (string) $row['base_price'];
        }

        return $map;
    }

    public function create(int $clinicId, string $name, string $price): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO procedures (clinic_id, name, base_price) VALUES (:c, :name, :price)
             ON CONFLICT (clinic_id, name) DO UPDATE SET base_price = EXCLUDED.base_price, is_active = TRUE'
        );
        $stmt->execute(['c' => $clinicId, 'name' => $name, 'price' => $price]);
    }

    public function deactivate(int $id, int $clinicId): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE procedures SET is_active = FALSE WHERE id = :id AND clinic_id = :c'
        );
        $stmt->execute(['id' => $id, 'c' => $clinicId]);

        return $stmt->rowCount() > 0;
    }
}
