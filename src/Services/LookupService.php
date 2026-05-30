<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\LookupRepository;
use App\Repositories\VisitReasonRepository;

final class LookupService
{
    private LookupRepository $lookups;
    private VisitReasonRepository $reasons;

    public function __construct()
    {
        $this->lookups = new LookupRepository();
        $this->reasons = new VisitReasonRepository();
    }

    public function reasons(int $clinicId): array
    {
        return $this->reasons->forClinic($clinicId);
    }

    public function reasonInClinic(string $name, int $clinicId): bool
    {
        return $this->reasons->existsActive($clinicId, $name);
    }

    public function vets(int $clinicId): array
    {
        return $this->lookups->vets($clinicId);
    }

    public function pets(int $clinicId): array
    {
        return $this->lookups->pets($clinicId);
    }

    public function species(): array
    {
        return $this->lookups->species();
    }

    public function vetInClinic(int $vetId, int $clinicId): bool
    {
        return $this->lookups->vetInClinic($vetId, $clinicId);
    }

    public function petInClinic(int $petId, int $clinicId): bool
    {
        return $this->lookups->petInClinic($petId, $clinicId);
    }

    public function clientInClinic(int $clientId, int $clinicId): bool
    {
        return $this->lookups->clientInClinic($clientId, $clinicId);
    }
}
