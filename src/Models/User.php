<?php

declare(strict_types=1);

namespace App\Models;

final class User
{
    public function __construct(
        public readonly int $id,
        public readonly string $email,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $role,
        public readonly string $passwordHash,
        public readonly int $clinicId,
    ) {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (string) $row['email'],
            (string) $row['first_name'],
            (string) $row['last_name'],
            (string) $row['role'],
            (string) $row['password_hash'],
            (int) $row['clinic_id'],
        );
    }

    public function fullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }
}
