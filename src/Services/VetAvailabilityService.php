<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\VetAvailabilityRepository;

final class VetAvailabilityService
{
    private VetAvailabilityRepository $availability;

    public function __construct()
    {
        $this->availability = new VetAvailabilityRepository();
    }

    public function forVet(int $vetId): array
    {
        return $this->availability->forVet($vetId);
    }

    public function isAvailable(int $vetId, int $weekday, string $startTime, string $endTime): bool
    {
        return $this->availability->isAvailable($vetId, $weekday, $startTime, $endTime);
    }

    public function save(int $vetId, array $days): array
    {
        $rows = [];

        foreach ($days as $weekday => $day) {
            if (empty($day['enabled'])) {
                continue;
            }

            $start = (string) ($day['start'] ?? '');
            $end = (string) ($day['end'] ?? '');

            if (preg_match('/^\d{2}:\d{2}$/', $start) !== 1 || preg_match('/^\d{2}:\d{2}$/', $end) !== 1 || $start >= $end) {
                return ['ok' => false, 'message' => 'Sprawdź godziny — koniec musi być późniejszy niż początek.'];
            }

            $rows[] = ['weekday' => (int) $weekday, 'start' => $start, 'end' => $end];
        }

        $this->availability->replaceForVet($vetId, $rows);

        return ['ok' => true, 'message' => 'Grafik dostępności został zapisany.'];
    }
}
