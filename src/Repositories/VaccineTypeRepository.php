<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class VaccineTypeRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function forClinic(int $clinicId): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, price, validity_months FROM vaccine_types
             WHERE clinic_id = :c AND is_active = TRUE ORDER BY name'
        );
        $stmt->execute(['c' => $clinicId]);

        return $stmt->fetchAll();
    }

    public function find(int $id, int $clinicId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, price, validity_months FROM vaccine_types
             WHERE id = :id AND clinic_id = :c AND is_active = TRUE'
        );
        $stmt->execute(['id' => $id, 'c' => $clinicId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function create(int $clinicId, string $name, string $price, int $validityMonths): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO vaccine_types (clinic_id, name, price, validity_months)
             VALUES (:c, :name, :price, :months)
             ON CONFLICT (clinic_id, name)
             DO UPDATE SET price = EXCLUDED.price, validity_months = EXCLUDED.validity_months, is_active = TRUE'
        );
        $stmt->execute(['c' => $clinicId, 'name' => $name, 'price' => $price, 'months' => $validityMonths]);
    }

    public function deactivate(int $id, int $clinicId): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE vaccine_types SET is_active = FALSE WHERE id = :id AND clinic_id = :c'
        );
        $stmt->execute(['id' => $id, 'c' => $clinicId]);

        return $stmt->rowCount() > 0;
    }
}
