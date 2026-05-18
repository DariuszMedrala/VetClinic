<?php

declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;

final class Pet
{
    public function __construct(
        public readonly int $id,
        public readonly int $clientId,
        public readonly int $speciesId,
        public readonly string $speciesName,
        public readonly string $name,
        public readonly ?string $breed,
        public readonly string $sex,
        public readonly ?DateTimeImmutable $birthDate,
        public readonly ?string $weightKg,
        public readonly string $ownerName,
        public readonly ?string $ownerPhone,
        public readonly int $loyaltyPoints,
        public readonly ?string $photoPath,
    ) {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (int) $row['client_id'],
            (int) $row['species_id'],
            (string) $row['species'],
            (string) $row['name'],
            $row['breed'] !== null ? (string) $row['breed'] : null,
            (string) $row['sex'],
            $row['birth_date'] !== null ? new DateTimeImmutable((string) $row['birth_date']) : null,
            $row['weight_kg'] !== null ? (string) $row['weight_kg'] : null,
            (string) $row['owner_name'],
            $row['owner_phone'] !== null ? (string) $row['owner_phone'] : null,
            (int) $row['loyalty_points'],
            $row['photo_path'] !== null ? (string) $row['photo_path'] : null,
        );
    }

    public function sexLabel(): string
    {
        return match ($this->sex) {
            'male' => 'Samiec',
            'female' => 'Samica',
            default => 'Nieznana',
        };
    }

    public function ageLabel(): string
    {
        if ($this->birthDate === null) {
            return '—';
        }

        $diff = $this->birthDate->diff(new DateTimeImmutable('today'));

        if ($diff->y < 1) {
            return $diff->m . ' mies.';
        }

        return $this->yearsLabel($diff->y);
    }

    public function weightLabel(): string
    {
        if ($this->weightKg === null) {
            return '—';
        }

        return number_format((float) $this->weightKg, 1, ',', ' ') . ' kg';
    }

    private function yearsLabel(int $years): string
    {
        if ($years === 1) {
            return '1 rok';
        }

        $mod10 = $years % 10;
        $mod100 = $years % 100;

        if ($mod10 >= 2 && $mod10 <= 4 && !($mod100 >= 12 && $mod100 <= 14)) {
            return $years . ' lata';
        }

        return $years . ' lat';
    }
}
