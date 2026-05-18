<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\AppointmentRepository;

final class AppointmentService
{
    private AppointmentRepository $appointments;

    public function __construct()
    {
        $this->appointments = new AppointmentRepository();
    }

    public function upcoming(int $clinicId): array
    {
        return $this->appointments->upcoming($clinicId);
    }

    public function forWeek(int $clinicId, string $from, string $to): array
    {
        return $this->appointments->forWeek($clinicId, $from, $to);
    }

    public function create(int $petId, int $vetId, string $startsAt, string $endsAt, string $reason): array
    {
        return match ($this->appointments->create($petId, $vetId, $startsAt, $endsAt, $reason)) {
            AppointmentRepository::CREATED => ['ok' => true, 'status' => 201, 'message' => 'Wizyta została dodana do harmonogramu.'],
            AppointmentRepository::CONFLICT => ['ok' => false, 'status' => 409, 'message' => 'Lekarz ma już wizytę w tym terminie (kolizja harmonogramu).'],
            default => ['ok' => false, 'status' => 500, 'message' => 'Nie udało się dodać wizyty.'],
        };
    }

    public function cancel(int $id, int $clinicId): array
    {
        return match ($this->appointments->cancel($id, $clinicId)) {
            AppointmentRepository::CANCELLED => ['ok' => true, 'status' => 200, 'message' => 'Wizyta została anulowana.'],
            AppointmentRepository::INVALID => ['ok' => false, 'status' => 409, 'message' => 'Tej wizyty nie można już anulować.'],
            default => ['ok' => false, 'status' => 404, 'message' => 'Nie znaleziono wizyty.'],
        };
    }
}
