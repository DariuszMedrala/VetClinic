<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class LoyaltyRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function settings(int $clinicId): array
    {
        $stmt = $this->db->prepare('SELECT points_per, per_amount FROM loyalty_settings WHERE clinic_id = :c');
        $stmt->execute(['c' => $clinicId]);
        $row = $stmt->fetch();

        return $row ?: ['points_per' => 1, 'per_amount' => '10.00'];
    }

    public function saveSettings(int $clinicId, int $pointsPer, string $perAmount): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO loyalty_settings (clinic_id, points_per, per_amount) VALUES (:c, :p, :a)
             ON CONFLICT (clinic_id) DO UPDATE SET points_per = EXCLUDED.points_per, per_amount = EXCLUDED.per_amount'
        );
        $stmt->execute(['c' => $clinicId, 'p' => $pointsPer, 'a' => $perAmount]);
    }

    public function tiers(int $clinicId): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, min_points, discount_percent FROM loyalty_tiers
             WHERE clinic_id = :c ORDER BY min_points'
        );
        $stmt->execute(['c' => $clinicId]);

        return $stmt->fetchAll();
    }

    public function addTier(int $clinicId, int $minPoints, string $percent): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO loyalty_tiers (clinic_id, min_points, discount_percent) VALUES (:c, :m, :d)
             ON CONFLICT (clinic_id, min_points) DO UPDATE SET discount_percent = EXCLUDED.discount_percent'
        );
        $stmt->execute(['c' => $clinicId, 'm' => $minPoints, 'd' => $percent]);
    }

    public function deleteTier(int $id, int $clinicId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM loyalty_tiers WHERE id = :id AND clinic_id = :c');
        $stmt->execute(['id' => $id, 'c' => $clinicId]);

        return $stmt->rowCount() > 0;
    }

    public function discountPercentFor(int $clinicId, int $points): float
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(MAX(discount_percent), 0) FROM loyalty_tiers
             WHERE clinic_id = :c AND min_points <= :p'
        );
        $stmt->execute(['c' => $clinicId, 'p' => $points]);

        return (float) $stmt->fetchColumn();
    }

    public function awardForInvoice(int $invoiceId): void
    {
        $stmt = $this->db->prepare(
            'UPDATE clients c
             SET loyalty_points = c.loyalty_points
                 + (floor(fn_calculate_invoice_total(a.id) / ls.per_amount) * ls.points_per)::int
             FROM invoices i
             JOIN appointments a ON a.id = i.appointment_id
             JOIN pets p ON p.id = a.pet_id
             JOIN users u ON u.id = p.client_id
             JOIN loyalty_settings ls ON ls.clinic_id = u.clinic_id
             WHERE i.id = :id AND c.user_id = p.client_id AND ls.per_amount > 0'
        );
        $stmt->execute(['id' => $invoiceId]);
    }
}
