<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\StatsRepository;

final class StatsService
{
    private StatsRepository $stats;

    public function __construct()
    {
        $this->stats = new StatsRepository();
    }

    public function forDashboard(int $clinicId): array
    {
        return [
            'appointmentsToday' => $this->stats->appointmentsToday($clinicId),
            'pendingInvoices' => $this->stats->pendingInvoices($clinicId),
            'overdueVaccinations' => $this->stats->overdueVaccinations($clinicId),
        ];
    }
}
