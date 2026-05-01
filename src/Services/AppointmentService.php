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

    public function upcoming(): array
    {
        return $this->appointments->upcoming();
    }

    public function cancel(int $id): array
    {
        return match ($this->appointments->cancel($id)) {
            AppointmentRepository::CANCELLED => ['ok' => true, 'status' => 200, 'message' => 'Wizyta została anulowana.'],
            AppointmentRepository::INVALID => ['ok' => false, 'status' => 409, 'message' => 'Tej wizyty nie można już anulować.'],
            default => ['ok' => false, 'status' => 404, 'message' => 'Nie znaleziono wizyty.'],
        };
    }
}
