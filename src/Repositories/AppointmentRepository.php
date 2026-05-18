<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\Appointment;
use PDO;
use PDOException;
use Throwable;

final class AppointmentRepository
{
    public const CANCELLED = 'cancelled';
    public const NOT_FOUND = 'not_found';
    public const INVALID = 'invalid';
    public const CREATED = 'created';
    public const CONFLICT = 'conflict';

    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function forWeek(int $clinicId, string $from, string $to): array
    {
        $stmt = $this->db->prepare(
            "SELECT appointment_id, starts_at, ends_at, status, reason,
                    vet_id, vet_name, room, pet_id, pet_name, species,
                    client_name, client_phone
             FROM vw_vet_weekly_schedule
             WHERE clinic_id = :clinic
               AND status <> 'cancelled'
               AND starts_at >= :from AND starts_at < :to
             ORDER BY starts_at"
        );
        $stmt->execute(['clinic' => $clinicId, 'from' => $from, 'to' => $to]);

        return array_map(
            static fn (array $row): Appointment => Appointment::fromRow($row),
            $stmt->fetchAll()
        );
    }

    public function upcomingForClient(int $clientId): array
    {
        $stmt = $this->db->prepare(
            "SELECT appointment_id, starts_at, ends_at, status, reason,
                    vet_id, vet_name, room, pet_id, pet_name, species,
                    client_name, client_phone
             FROM vw_vet_weekly_schedule
             WHERE client_id = :id
               AND status IN ('scheduled', 'confirmed', 'in_progress')
               AND starts_at >= date_trunc('day', now())
             ORDER BY starts_at"
        );
        $stmt->execute(['id' => $clientId]);

        return array_map(
            static fn (array $row): Appointment => Appointment::fromRow($row),
            $stmt->fetchAll()
        );
    }

    public function historyForPet(int $petId): array
    {
        $stmt = $this->db->prepare(
            "SELECT a.id, a.starts_at, a.status, a.reason,
                    vp.title || ' ' || vu.first_name || ' ' || vu.last_name AS vet_name,
                    COALESCE(string_agg(pr.name, ', ' ORDER BY pr.name), '—') AS procedures,
                    COUNT(ap.procedure_id) AS procedure_count
             FROM appointments a
             JOIN vet_profiles vp ON vp.user_id = a.vet_id
             JOIN users vu ON vu.id = vp.user_id
             LEFT JOIN appointment_procedures ap ON ap.appointment_id = a.id
             LEFT JOIN procedures pr ON pr.id = ap.procedure_id
             WHERE a.pet_id = :id
             GROUP BY a.id, vp.title, vu.first_name, vu.last_name
             ORDER BY a.starts_at DESC"
        );
        $stmt->execute(['id' => $petId]);

        return $stmt->fetchAll();
    }

    public function create(int $petId, int $vetId, string $startsAt, string $endsAt, string $reason): string
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO appointments (pet_id, vet_id, starts_at, ends_at, reason, status)
                 VALUES (:pet, :vet, :starts, :ends, :reason, 'scheduled')"
            );
            $stmt->execute([
                'pet' => $petId,
                'vet' => $vetId,
                'starts' => $startsAt,
                'ends' => $endsAt,
                'reason' => $reason,
            ]);

            return self::CREATED;
        } catch (PDOException $exception) {
            if ($exception->getCode() === 'P0001') {
                return self::CONFLICT;
            }

            throw $exception;
        }
    }

    public function upcoming(int $clinicId, int $limit = 20): array
    {
        $stmt = $this->db->prepare(
            "SELECT appointment_id, starts_at, ends_at, status, reason,
                    vet_id, vet_name, room, pet_id, pet_name, species,
                    client_name, client_phone
             FROM vw_vet_weekly_schedule
             WHERE clinic_id = :clinic
               AND status IN ('scheduled', 'confirmed', 'in_progress')
               AND starts_at >= date_trunc('day', now())
             ORDER BY starts_at
             LIMIT :limit"
        );
        $stmt->bindValue(':clinic', $clinicId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(
            static fn (array $row): Appointment => Appointment::fromRow($row),
            $stmt->fetchAll()
        );
    }

    public function cancel(int $id, int $clinicId): string
    {
        $this->db->beginTransaction();

        try {
            $this->db->exec('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');

            $stmt = $this->db->prepare(
                'SELECT a.status FROM appointments a
                 JOIN users vu ON vu.id = a.vet_id
                 WHERE a.id = :id AND vu.clinic_id = :c
                 FOR UPDATE OF a'
            );
            $stmt->execute(['id' => $id, 'c' => $clinicId]);
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
