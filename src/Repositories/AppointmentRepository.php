<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\Appointment;
use PDO;
use Throwable;

final class AppointmentRepository
{
    public const CANCELLED = 'cancelled';
    public const NOT_FOUND = 'not_found';
    public const INVALID = 'invalid';

    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function upcoming(int $limit = 20): array
    {
        $stmt = $this->db->prepare(
            "SELECT appointment_id, starts_at, ends_at, status, reason,
                    vet_id, vet_name, room, pet_id, pet_name, species,
                    client_name, client_phone
             FROM vw_vet_weekly_schedule
             WHERE status IN ('scheduled', 'confirmed', 'in_progress')
               AND starts_at >= date_trunc('day', now())
             ORDER BY starts_at
             LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(
            static fn (array $row): Appointment => Appointment::fromRow($row),
            $stmt->fetchAll()
        );
    }

    public function cancel(int $id): string
    {
        $this->db->beginTransaction();

        try {
            $this->db->exec('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');

            $stmt = $this->db->prepare('SELECT status FROM appointments WHERE id = :id FOR UPDATE');
            $stmt->execute(['id' => $id]);
            $status = $stmt->fetchColumn();

            if ($status === false) {
                $this->db->rollBack();

                return self::NOT_FOUND;
            }

            if (!in_array($status, ['scheduled', 'confirmed'], true)) {
                $this->db->rollBack();

                return self::INVALID;
            }

            $update = $this->db->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = :id");
            $update->execute(['id' => $id]);

            $this->db->commit();

            return self::CANCELLED;
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $exception;
        }
    }
}
