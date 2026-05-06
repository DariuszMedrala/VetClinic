<?php

declare(strict_types=1);

namespace App\Models;

final class Client
{
    public function __construct(
        public readonly int $userId,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $email,
        public readonly ?string $phone,
        public readonly int $loyaltyPoints,
    ) {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['user_id'],
            (string) $row['first_name'],
            (string) $row['last_name'],
            (string) $row['email'],
            $row['phone'] !== null ? (string) $row['phone'] : null,
            (int) $row['loyalty_points'],
        );
    }

    public function fullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }
}
