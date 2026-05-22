<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;
use Throwable;

final class VetAvailabilityRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function forVet(int $vetId): array
    {
        $stmt = $this->db->prepare(
            'SELECT weekday, to_char(start_time, \'HH24:MI\') AS start_time, to_char(end_time, \'HH24:MI\') AS end_time
             FROM vet_availability WHERE vet_id = :vet ORDER BY weekday'
        );
        $stmt->execute(['vet' => $vetId]);

        $result = [];
        foreach ($stmt->fetchAll() as $row) {
            $result[(int) $row['weekday']] = ['start' => $row['start_time'], 'end' => $row['end_time']];
        }

        return $result;
    }

    public function replaceForVet(int $vetId, array $rows): void
    {
        $this->db->beginTransaction();

        try {
            $delete = $this->db->prepare('DELETE FROM vet_availability WHERE vet_id = :vet');
            $delete->execute(['vet' => $vetId]);

            $insert = $this->db->prepare(
                'INSERT INTO vet_availability (vet_id, weekday, start_time, end_time)
                 VALUES (:vet, :weekday, :start, :end)'
            );

            foreach ($rows as $row) {
                $insert->execute([
                    'vet' => $vetId,
                    'weekday' => $row['weekday'],
                    'start' => $row['start'],
                    'end' => $row['end'],
                ]);
            }

            $this->db->commit();
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $exception;
        }
    }

    public function isAvailable(int $vetId, int $weekday, string $startTime, string $endTime): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM vet_availability
             WHERE vet_id = :vet AND weekday = :weekday
               AND start_time <= CAST(:start AS time) AND end_time >= CAST(:end AS time)'
        );
        $stmt->execute(['vet' => $vetId, 'weekday' => $weekday, 'start' => $startTime, 'end' => $endTime]);

        return (bool) $stmt->fetchColumn();
    }
}
