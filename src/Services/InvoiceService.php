<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\InvoiceRepository;
use App\Repositories\LoyaltyRepository;

final class InvoiceService
{
    private InvoiceRepository $invoices;
    private LoyaltyRepository $loyalty;

    public function __construct()
    {
        $this->invoices = new InvoiceRepository();
        $this->loyalty = new LoyaltyRepository();
    }

    public function all(int $clinicId): array
    {
        return $this->invoices->all($clinicId);
    }

    public function detail(int $id, int $clinicId): ?array
    {
        $invoice = $this->invoices->find($id, $clinicId);

        if ($invoice === null) {
            return null;
        }

        return [
            'invoice' => $invoice,
            'items' => $this->invoices->lineItems($invoice->appointmentId),
        ];
    }

    public function pay(int $id, int $clinicId, string $method): array
    {
        $result = $this->invoices->pay($id, $clinicId, $method);

        if ($result === InvoiceRepository::PAID) {
            $this->loyalty->awardForInvoice($id);
        }

        return match ($result) {
            InvoiceRepository::PAID => ['ok' => true, 'status' => 200, 'message' => 'Płatność została zarejestrowana.'],
            InvoiceRepository::INVALID => ['ok' => false, 'status' => 409, 'message' => 'Ta faktura nie oczekuje już na płatność.'],
            default => ['ok' => false, 'status' => 404, 'message' => 'Nie znaleziono faktury.'],
        };
    }
}
