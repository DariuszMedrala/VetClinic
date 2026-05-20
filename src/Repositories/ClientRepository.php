<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\Client;
use PDO;

final class ClientRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function find(int $userId): ?Client
    {
        $stmt = $this->db->prepare(
            'SELECT u.id AS user_id, u.first_name, u.last_name, u.email, c.phone, c.loyalty_points
             FROM clients c
             JOIN users u ON u.id = c.user_id
             WHERE c.user_id = :id'
        );
        $stmt->execute(['id' => $userId]);
        $row = $stmt->fetch();

        return $row ? Client::fromRow($row) : null;
    }

    public function updatePhone(int $userId, ?string $phone): void
    {
        $stmt = $this->db->prepare('UPDATE clients SET phone = :phone WHERE user_id = :id');
        $stmt->execute(['phone' => $phone, 'id' => $userId]);
    }

    public function all(int $clinicId): array
    {
        $stmt = $this->db->prepare(
            'SELECT u.id AS user_id, u.first_name, u.last_name, u.email, c.phone, c.loyalty_points
             FROM clients c
             JOIN users u ON u.id = c.user_id
             WHERE u.is_active = TRUE AND u.clinic_id = :c
             ORDER BY u.last_name, u.first_name'
        );
        $stmt->execute(['c' => $clinicId]);

        return array_map(
            static fn (array $row): Client => Client::fromRow($row),
            $stmt->fetchAll()
        );
    }
}
