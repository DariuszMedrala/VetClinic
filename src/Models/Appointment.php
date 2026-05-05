<?php

declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;

final class Appointment
{
    public function __construct(
        public readonly int $id,
        public readonly DateTimeImmutable $startsAt,
        public readonly DateTimeImmutable $endsAt,
        public readonly string $status,
        public readonly string $reason,
        public readonly int $vetId,
        public readonly string $vetName,
        public readonly ?string $room,
        public readonly int $petId,
        public readonly string $petName,
        public readonly string $species,
        public readonly string $clientName,
        public readonly ?string $clientPhone,
    ) {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['appointment_id'],
            new DateTimeImmutable((string) $row['starts_at']),
            new DateTimeImmutable((string) $row['ends_at']),
            (string) $row['status'],
            (string) $row['reason'],
            (int) $row['vet_id'],
            (string) $row['vet_name'],
            $row['room'] !== null ? (string) $row['room'] : null,
            (int) $row['pet_id'],
            (string) $row['pet_name'],
            (string) $row['species'],
            (string) $row['client_name'],
            $row['client_phone'] !== null ? (string) $row['client_phone'] : null,
        );
    }

    public function time(): string
    {
        return $this->startsAt->format('H:i');
    }

    public function day(): string
    {
        return $this->startsAt->format('Y-m-d');
    }

    public function dateShort(): string
    {
        return $this->startsAt->format('d.m');
    }

    public function weekdayShort(): string
    {
        return match ($this->startsAt->format('N')) {
            '1' => 'pon',
            '2' => 'wt',
            '3' => 'śr',
            '4' => 'czw',
            '5' => 'pt',
            '6' => 'sob',
            '7' => 'ndz',
            default => '',
        };
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, ['scheduled', 'confirmed'], true);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'scheduled' => 'Zaplanowana',
            'confirmed' => 'Potwierdzona',
            'in_progress' => 'W trakcie',
            'completed' => 'Zakończona',
            'cancelled' => 'Anulowana',
            default => $this->status,
        };
    }

    public function badgeClass(): string
    {
        return match ($this->status) {
            'confirmed' => 'badge--confirmed',
            'in_progress' => 'badge--progress',
            'completed' => 'badge--uptodate',
            'cancelled' => 'badge--overdue',
            default => 'badge--waiting',
        };
    }
}
