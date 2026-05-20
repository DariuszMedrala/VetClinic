<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\User;
use PDO;

final class UserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare(
            'SELECT id, email, password_hash, first_name, last_name, role, clinic_id
             FROM users
             WHERE email = :email AND is_active = TRUE'
        );
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();

        return $row ? User::fromRow($row) : null;
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);

        return (bool) $stmt->fetchColumn();
    }

    public function create(string $email, string $passwordHash, string $firstName, string $lastName, string $role, int $clinicId): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (email, password_hash, first_name, last_name, role, clinic_id)
             VALUES (:email, :hash, :first, :last, CAST(:role AS user_role), :clinic)
             RETURNING id'
        );
        $stmt->execute([
            'email' => $email,
            'hash' => $passwordHash,
            'first' => $firstName,
            'last' => $lastName,
            'role' => $role,
            'clinic' => $clinicId,
        ]);

        return (int) $stmt->fetchColumn();
    }

    public function createVetProfile(int $userId, string $licenseNumber): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO vet_profiles (user_id, license_number) VALUES (:id, :license)'
        );
        $stmt->execute(['id' => $userId, 'license' => $licenseNumber]);
    }

    public function createClientProfile(int $userId, ?string $phone): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO clients (user_id, phone) VALUES (:id, :phone)'
        );
        $stmt->execute(['id' => $userId, 'phone' => $phone]);
    }

    public function updatePassword(int $userId, string $passwordHash): void
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET password_hash = :hash, updated_at = now() WHERE id = :id'
        );
        $stmt->execute(['hash' => $passwordHash, 'id' => $userId]);
    }
}
