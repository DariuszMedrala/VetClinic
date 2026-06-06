<?php

declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;

final class Invoice
{
    public function __construct(
        public readonly int $id,
        public readonly int $appointmentId,
        public readonly string $number,
        public readonly string $status,
        public readonly ?string $paymentMethod,
        public readonly DateTimeImmutable $issuedAt,
        public readonly ?DateTimeImmutable $paidAt,
        public readonly string $petName,
        public readonly string $species,
        public readonly string $clientName,
        public readonly int $loyaltyPoints,
        public readonly string $subtotal,
        public readonly string $total,
    ) {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (int) $row['appointment_id'],
            (string) $row['invoice_number'],
            (string) $row['status'],
            $row['payment_method'] !== null ? (string) $row['payment_method'] : null,
            new DateTimeImmutable((string) $row['issued_at']),
            $row['paid_at'] !== null ? new DateTimeImmutable((string) $row['paid_at']) : null,
            (string) $row['pet_name'],
            (string) $row['species'],
            (string) $row['client_name'],
            (int) $row['loyalty_points'],
            (string) $row['subtotal'],
            (string) $row['total'],
        );
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function hasDiscount(): bool
    {
        return (float) $this->total < (float) $this->subtotal;
    }

    public function discountPercent(): int
    {
        if ((float) $this->subtotal <= 0) {
            return 0;
        }

        return (int) round((1 - (float) $this->total / (float) $this->subtotal) * 100);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'paid' => 'Opłacona',
            'pending' => 'Oczekuje',
            'cancelled' => 'Anulowana',
            default => $this->status,
        };
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            'paid' => 'badge--uptodate',
            'cancelled' => 'badge--overdue',
            default => 'badge--pending',
        };
    }

    public function paymentLabel(): string
    {
        return match ($this->paymentMethod) {
            'card' => 'Karta',
            'cash' => 'Gotówka',
            'insurance' => 'Ubezpieczenie',
            default => '—',
        };
    }

    public function subtotalLabel(): string
    {
        return self::money($this->subtotal);
    }

    public function totalLabel(): string
    {
        return self::money($this->total);
    }

    public function discountLabel(): string
    {
        return '−' . self::money((string) ((float) $this->subtotal - (float) $this->total));
    }

    public static function money(string $value): string
    {
        return number_format((float) $value, 2, ',', ' ') . ' zł';
    }
}
