<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ProcedureRepository;
use App\Repositories\VaccineTypeRepository;
use App\Repositories\VisitReasonRepository;

final class CatalogService
{
    private VisitReasonRepository $reasons;
    private VaccineTypeRepository $vaccines;
    private ProcedureRepository $procedures;

    public function __construct()
    {
        $this->reasons = new VisitReasonRepository();
        $this->vaccines = new VaccineTypeRepository();
        $this->procedures = new ProcedureRepository();
    }

    public function forClinic(int $clinicId): array
    {
        return [
            'reasons' => $this->reasons->forClinic($clinicId),
            'vaccines' => $this->vaccines->forClinic($clinicId),
            'procedures' => $this->procedures->all($clinicId),
        ];
    }

    public function addReason(int $clinicId, string $name): array
    {
        if ($name === '') {
            return ['ok' => false, 'message' => 'Podaj nazwę powodu wizyty.'];
        }

        $this->reasons->create($clinicId, $name);

        return ['ok' => true, 'message' => 'Dodano powód wizyty.'];
    }

    public function addVaccine(int $clinicId, string $name, string $price, string $months): array
    {
        if ($name === '') {
            return ['ok' => false, 'message' => 'Podaj nazwę szczepionki.'];
        }

        if (!is_numeric($price) || (float) $price < 0) {
            return ['ok' => false, 'message' => 'Cena musi być liczbą nieujemną.'];
        }

        if (!ctype_digit($months) || (int) $months < 1) {
            return ['ok' => false, 'message' => 'Ważność musi być liczbą miesięcy większą od zera.'];
        }

        $this->vaccines->create($clinicId, $name, $price, (int) $months);

        return ['ok' => true, 'message' => 'Dodano szczepionkę.'];
    }

    public function addProcedure(int $clinicId, string $name, string $price): array
    {
        if ($name === '') {
            return ['ok' => false, 'message' => 'Podaj nazwę zabiegu.'];
        }

        if (!is_numeric($price) || (float) $price < 0) {
            return ['ok' => false, 'message' => 'Cena musi być liczbą nieujemną.'];
        }

        $this->procedures->create($clinicId, $name, $price);

        return ['ok' => true, 'message' => 'Dodano zabieg.'];
    }

    public function remove(string $type, int $id, int $clinicId): array
    {
        $removed = match ($type) {
            'reasons' => $this->reasons->deactivate($id, $clinicId),
            'vaccines' => $this->vaccines->deactivate($id, $clinicId),
            'treatments' => $this->procedures->deactivate($id, $clinicId),
            default => false,
        };

        return $removed
            ? ['ok' => true, 'message' => 'Pozycja została usunięta.']
            : ['ok' => false, 'message' => 'Nie znaleziono pozycji.'];
    }
}
