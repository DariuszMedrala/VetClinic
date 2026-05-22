<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class ProcedureRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function all(): array
    {
        return $this->db->query('SELECT id, name, description, base_price FROM procedures ORDER BY name')->fetchAll();
    }

    public function priceMap(): array
    {
        $map = [];
        foreach ($this->all() as $row) {
            $map[(int) $row['id']] = (string) $row['base_price'];
        }

        return $map;
    }
}
