<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class VisitReasonRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function forClinic(int $clinicId): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, name FROM visit_reasons WHERE clinic_id = :c AND is_active = TRUE ORDER BY name'
        );
        $stmt->execute(['c' => $clinicId]);

        return $stmt->fetchAll();
    }

    public function existsActive(int $clinicId, string $name): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM visit_reasons WHERE clinic_id = :c AND name = :name AND is_active = TRUE'
        );
        $stmt->execute(['c' => $clinicId, 'name' => $name]);

        return $stmt->fetchColumn() !== false;
    }

    public function create(int $clinicId, string $name): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO visit_reasons (clinic_id, name) VALUES (:c, :name)
             ON CONFLICT (clinic_id, name) DO UPDATE SET is_active = TRUE'
        );
        $stmt->execute(['c' => $clinicId, 'name' => $name]);
    }

    public function deactivate(int $id, int $clinicId): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE visit_reasons SET is_active = FALSE WHERE id = :id AND clinic_id = :c'
        );
        $stmt->execute(['id' => $id, 'c' => $clinicId]);

        return $stmt->rowCount() > 0;
    }
}
