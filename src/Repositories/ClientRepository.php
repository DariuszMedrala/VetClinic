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

    public function all(): array
    {
        $sql = 'SELECT u.id AS user_id, u.first_name, u.last_name, u.email, c.phone, c.loyalty_points
                FROM clients c
                JOIN users u ON u.id = c.user_id
                WHERE u.is_active = TRUE
                ORDER BY u.last_name, u.first_name';

        return array_map(
            static fn (array $row): Client => Client::fromRow($row),
            $this->db->query($sql)->fetchAll()
        );
    }
}
