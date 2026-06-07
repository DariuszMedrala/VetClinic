<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\AppointmentRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\ProcedureRepository;
use App\Repositories\VaccineTypeRepository;

final class BillingService
{
    private InvoiceRepository $invoices;
    private ProcedureRepository $procedures;
    private VaccineTypeRepository $vaccines;
    private AppointmentRepository $appointments;

    public function __construct()
    {
        $this->invoices = new InvoiceRepository();
        $this->procedures = new ProcedureRepository();
        $this->vaccines = new VaccineTypeRepository();
        $this->appointments = new AppointmentRepository();
    }

    public function invoiceableAppointment(int $appointmentId, int $vetId): ?array
    {
        return $this->invoices->appointmentForInvoice($appointmentId, $vetId);
    }

    public function procedures(int $clinicId): array
    {
        return $this->procedures->all($clinicId);
    }

    public function vaccines(int $clinicId): array
    {
        return $this->vaccines->forClinic($clinicId);
    }

    public function create(int $appointmentId, int $vetId, int $clinicId, array $quantities, ?int $vaccineTypeId = null): array
    {
        if ($this->invoices->appointmentForInvoice($appointmentId, $vetId) === null) {
            return ['ok' => false, 'message' => 'Tej wizyty nie można już zafakturować.'];
        }

        $prices = $this->procedures->priceMap($clinicId);
        $items = [];

        foreach ($quantities as $procedureId => $quantity) {
            $procedureId = (int) $procedureId;
            $quantity = (int) $quantity;

            if ($quantity > 0 && isset($prices[$procedureId])) {
                $items[] = ['procedure_id' => $procedureId, 'quantity' => $quantity, 'unit_price' => $prices[$procedureId]];
            }
        }

        if ($items === []) {
            return ['ok' => false, 'message' => 'Dodaj przynajmniej jedną pozycję (ilość większa od zera).'];
        }

        $invoiceId = $this->invoices->createForAppointment($appointmentId, $items);

        if ($vaccineTypeId !== null && $vaccineTypeId > 0) {
            $this->appointments->recordVaccination($appointmentId, $vaccineTypeId, $vetId);
        }

        return ['ok' => true, 'invoiceId' => $invoiceId, 'message' => 'Faktura została wystawiona.'];
    }
}
