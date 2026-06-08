<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\Appointment;
use DateTimeImmutable;
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
    public const COMPLETED = 'completed';
    public const TOO_EARLY = 'too_early';

    private const VIEW_COLUMNS = 'appointment_id, starts_at, ends_at, status, reason,
                vet_id, vet_name, room, pet_id, pet_name, species, client_name, client_phone';

    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function upcomingForVet(int $vetId, int $limit = 30): array
    {
        $stmt = $this->db->prepare(
            'SELECT ' . self::VIEW_COLUMNS . "
             FROM vw_vet_weekly_schedule
             WHERE vet_id = :vet
               AND status IN ('scheduled', 'confirmed', 'in_progress')
               AND starts_at >= date_trunc('day', now())
             ORDER BY starts_at
             LIMIT :limit"
        );
        $stmt->bindValue(':vet', $vetId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(static fn (array $r): Appointment => Appointment::fromRow($r), $stmt->fetchAll());
    }

    public function toInvoiceForVet(int $vetId): array
    {
        $stmt = $this->db->prepare(
            'SELECT ' . self::VIEW_COLUMNS . "
             FROM vw_vet_weekly_schedule v
             WHERE v.vet_id = :vet AND v.status = 'completed'
               AND NOT EXISTS (SELECT 1 FROM invoices i WHERE i.appointment_id = v.appointment_id)
             ORDER BY v.starts_at DESC"
        );
        $stmt->execute(['vet' => $vetId]);

        return array_map(static fn (array $r): Appointment => Appointment::fromRow($r), $stmt->fetchAll());
    }

    public function markCompleted(int $id, int $vetId, ?string $notes = null): string
    {
        $stmt = $this->db->prepare(
            "SELECT status, (starts_at + interval '15 minutes' <= now())::int AS can_complete
             FROM appointments WHERE id = :id AND vet_id = :vet"
        );
        $stmt->execute(['id' => $id, 'vet' => $vetId]);
        $row = $stmt->fetch();

        if ($row === false) {
            return self::NOT_FOUND;
        }

        if (!in_array($row['status'], ['scheduled', 'confirmed', 'in_progress'], true)) {
            return self::INVALID;
        }

        if ((int) $row['can_complete'] === 0) {
            return self::TOO_EARLY;
        }

        $update = $this->db->prepare(
            "UPDATE appointments SET status = 'completed', notes = COALESCE(NULLIF(:notes, ''), notes) WHERE id = :id"
        );
        $update->execute(['id' => $id, 'notes' => $notes]);

        return self::COMPLETED;
    }

    public function forWeek(int $clinicId, string $from, string $to, ?int $vetId = null): array
    {
        $params = ['clinic' => $clinicId, 'from' => $from, 'to' => $to];
        $vetFilter = '';

        if ($vetId !== null) {
            $vetFilter = ' AND vet_id = :vet';
            $params['vet'] = $vetId;
        }

        $stmt = $this->db->prepare(
            "SELECT appointment_id, starts_at, ends_at, status, reason,
                    vet_id, vet_name, room, pet_id, pet_name, species,
                    client_name, client_phone, breed, notes
             FROM vw_vet_weekly_schedule
             WHERE clinic_id = :clinic
               AND status <> 'cancelled'
               AND starts_at >= :from AND starts_at < :to" . $vetFilter . "
             ORDER BY starts_at"
        );
        $stmt->execute($params);

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

    public function confirmForClient(int $appointmentId, int $clientId): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE appointments SET status = 'confirmed'
             WHERE id = :id AND status = 'scheduled'
               AND pet_id IN (SELECT id FROM pets WHERE client_id = :client)"
        );
        $stmt->execute(['id' => $appointmentId, 'client' => $clientId]);

        return $stmt->rowCount() > 0;
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

    public function recordVaccination(int $appointmentId, int $vaccineTypeId, int $vetId): void
    {
        $stmt = $this->db->prepare(
            'SELECT a.pet_id, a.starts_at::date AS administered_at, vt.name, vt.validity_months
             FROM appointments a
             JOIN pets p ON p.id = a.pet_id
             JOIN users u ON u.id = p.client_id
             JOIN vaccine_types vt ON vt.id = :vaccine AND vt.clinic_id = u.clinic_id AND vt.is_active = TRUE
             WHERE a.id = :id'
        );
        $stmt->execute(['vaccine' => $vaccineTypeId, 'id' => $appointmentId]);
        $row = $stmt->fetch();

        if ($row === false) {
            return;
        }

        $administeredAt = (string) $row['administered_at'];
        $expiresAt = (new DateTimeImmutable($administeredAt))
            ->modify('+' . (int) $row['validity_months'] . ' months')
            ->format('Y-m-d');

        $update = $this->db->prepare(
            'UPDATE vaccinations
             SET administered_at = :a, expires_at = :e, administered_by = :vet, external_clinic = NULL
             WHERE id = (
                 SELECT id FROM vaccinations
                 WHERE pet_id = :pet AND vaccine_name = :name
                 ORDER BY administered_at DESC LIMIT 1
             )'
        );
        $update->execute([
            'a' => $administeredAt,
            'e' => $expiresAt,
            'vet' => $vetId,
            'pet' => (int) $row['pet_id'],
            'name' => $row['name'],
        ]);

        if ($update->rowCount() === 0) {
            $insert = $this->db->prepare(
                'INSERT INTO vaccinations (pet_id, vaccine_name, administered_at, expires_at, administered_by)
                 VALUES (:pet, :name, :a, :e, :vet)'
            );
            $insert->execute([
                'pet' => (int) $row['pet_id'],
                'name' => $row['name'],
                'a' => $administeredAt,
                'e' => $expiresAt,
                'vet' => $vetId,
            ]);
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
