<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class PasswordResetRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function create(int $userId, string $tokenHash, string $expiresAt): void
    {
        $this->deleteForUser($userId);

        $stmt = $this->db->prepare(
            'INSERT INTO password_resets (user_id, token_hash, expires_at)
             VALUES (:user, :hash, :expires)'
        );
        $stmt->execute(['user' => $userId, 'hash' => $tokenHash, 'expires' => $expiresAt]);
    }

    public function findUserByToken(string $tokenHash): ?int
    {
        $stmt = $this->db->prepare(
            'SELECT user_id FROM password_resets
             WHERE token_hash = :hash AND expires_at > now()'
        );
        $stmt->execute(['hash' => $tokenHash]);
        $id = $stmt->fetchColumn();

        return $id === false ? null : (int) $id;
    }

    public function deleteForUser(int $userId): void
    {
        $stmt = $this->db->prepare('DELETE FROM password_resets WHERE user_id = :user');
        $stmt->execute(['user' => $userId]);
    }
}
