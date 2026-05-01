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

    public function forDashboard(): array
    {
        return [
            'appointmentsToday' => $this->stats->appointmentsToday(),
            'pendingInvoices' => $this->stats->pendingInvoices(),
            'overdueVaccinations' => $this->stats->overdueVaccinations(),
        ];
    }
}
