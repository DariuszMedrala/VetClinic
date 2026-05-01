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

    public function appointmentsToday(): int
    {
        return $this->count(
            "SELECT count(*) FROM appointments
             WHERE starts_at::date = CURRENT_DATE AND status <> 'cancelled'"
        );
    }

    public function pendingInvoices(): int
    {
        return $this->count("SELECT count(*) FROM invoices WHERE status = 'pending'");
    }

    public function overdueVaccinations(): int
    {
        return $this->count("SELECT count(*) FROM vw_pet_vaccination_status WHERE status = 'overdue'");
    }

    private function count(string $sql): int
    {
        return (int) $this->db->query($sql)->fetchColumn();
    }
}
