<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\LookupRepository;

final class LookupService
{
    private LookupRepository $lookups;

    public function __construct()
    {
        $this->lookups = new LookupRepository();
    }

    public function vets(): array
    {
        return $this->lookups->vets();
    }

    public function pets(): array
    {
        return $this->lookups->pets();
    }

    public function species(): array
    {
        return $this->lookups->species();
    }
}
