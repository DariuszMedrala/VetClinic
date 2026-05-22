<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class StatsRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function appointmentsToday(int $clinicId): int
    {
        return $this->count(
            "SELECT count(*) FROM appointments a
             JOIN users vu ON vu.id = a.vet_id
             WHERE vu.clinic_id = :c AND a.starts_at::date = CURRENT_DATE AND a.status <> 'cancelled'",
            $clinicId
        );
    }

    public function pendingInvoices(int $clinicId): int
    {
        return $this->count(
            "SELECT count(*) FROM invoices i
             JOIN appointments a ON a.id = i.appointment_id
             JOIN users vu ON vu.id = a.vet_id
             WHERE vu.clinic_id = :c AND i.status = 'pending'",
            $clinicId
        );
    }

    public function appointmentsTodayForVet(int $vetId): int
    {
        $stmt = $this->db->prepare(
            "SELECT count(*) FROM appointments
             WHERE vet_id = :vet AND starts_at::date = CURRENT_DATE AND status <> 'cancelled'"
        );
        $stmt->execute(['vet' => $vetId]);

        return (int) $stmt->fetchColumn();
    }

    public function overdueVaccinations(int $clinicId): int
    {
        return $this->count(
            "SELECT count(*) FROM vw_pet_vaccination_status WHERE clinic_id = :c AND status = 'overdue'",
            $clinicId
        );
    }

    private function count(string $sql, int $clinicId): int
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['c' => $clinicId]);

        return (int) $stmt->fetchColumn();
    }
}
