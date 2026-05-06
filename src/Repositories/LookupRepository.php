<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class LookupRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function vets(): array
    {
        $sql = "SELECT vp.user_id AS id,
                       vp.title || ' ' || u.first_name || ' ' || u.last_name AS name
                FROM vet_profiles vp
                JOIN users u ON u.id = vp.user_id
                WHERE u.is_active = TRUE
                ORDER BY name";

        return $this->db->query($sql)->fetchAll();
    }

    public function species(): array
    {
        return $this->db->query('SELECT id, name FROM species ORDER BY name')->fetchAll();
    }

    public function pets(): array
    {
        $sql = "SELECT p.id,
                       p.name || ' (' || s.name || ') — ' || cu.first_name || ' ' || cu.last_name AS label
                FROM pets p
                JOIN species s ON s.id = p.species_id
                JOIN clients c ON c.user_id = p.client_id
                JOIN users cu ON cu.id = c.user_id
                ORDER BY p.name";

        return $this->db->query($sql)->fetchAll();
    }
}
