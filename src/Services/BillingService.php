<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\InvoiceRepository;
use App\Repositories\ProcedureRepository;

final class BillingService
{
    private InvoiceRepository $invoices;
    private ProcedureRepository $procedures;

    public function __construct()
    {
        $this->invoices = new InvoiceRepository();
        $this->procedures = new ProcedureRepository();
    }

    public function invoiceableAppointment(int $appointmentId, int $vetId): ?array
    {
        return $this->invoices->appointmentForInvoice($appointmentId, $vetId);
    }

    public function procedures(): array
    {
        return $this->procedures->all();
    }

    public function create(int $appointmentId, int $vetId, array $quantities): array
    {
        if ($this->invoices->appointmentForInvoice($appointmentId, $vetId) === null) {
            return ['ok' => false, 'message' => 'Tej wizyty nie można już zafakturować.'];
        }

        $prices = $this->procedures->priceMap();
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

        return ['ok' => true, 'invoiceId' => $invoiceId, 'message' => 'Faktura została wystawiona.'];
    }
}
