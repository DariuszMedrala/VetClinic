<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class LoginAttemptRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function countRecent(string $ip, int $windowMinutes): int
    {
        $stmt = $this->db->prepare(
            "SELECT count(*) FROM login_attempts
             WHERE ip = :ip AND attempted_at > now() - make_interval(mins => :win)"
        );
        $stmt->execute(['ip' => $ip, 'win' => $windowMinutes]);

        return (int) $stmt->fetchColumn();
    }

    public function record(string $ip): void
    {
        $stmt = $this->db->prepare('INSERT INTO login_attempts (ip) VALUES (:ip)');
        $stmt->execute(['ip' => $ip]);

        $this->db->exec("DELETE FROM login_attempts WHERE attempted_at < now() - interval '1 day'");
    }

    public function clear(string $ip): void
    {
        $stmt = $this->db->prepare('DELETE FROM login_attempts WHERE ip = :ip');
        $stmt->execute(['ip' => $ip]);
    }
}
