<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\Invoice;
use PDO;

final class InvoiceRepository
{
    public const PAID = 'paid';
    public const NOT_FOUND = 'not_found';
    public const INVALID = 'invalid';

    private const COLUMNS = "i.id, i.appointment_id, i.invoice_number, i.status, i.payment_method,
                i.issued_at, i.paid_at,
                p.name AS pet_name, s.name AS species,
                cu.first_name || ' ' || cu.last_name AS client_name,
                c.loyalty_points,
                COALESCE((SELECT SUM(ap.unit_price * ap.quantity)
                          FROM appointment_procedures ap
                          WHERE ap.appointment_id = i.appointment_id), 0) AS subtotal,
                fn_calculate_invoice_total(i.appointment_id) AS total";

    private const JOINS = 'FROM invoices i
                JOIN appointments a ON a.id = i.appointment_id
                JOIN pets p ON p.id = a.pet_id
                JOIN species s ON s.id = p.species_id
                JOIN clients c ON c.user_id = p.client_id
                JOIN users cu ON cu.id = c.user_id';

    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function all(): array
    {
        $sql = 'SELECT ' . self::COLUMNS . ' ' . self::JOINS . ' ORDER BY i.issued_at DESC';

        return array_map(
            static fn (array $row): Invoice => Invoice::fromRow($row),
            $this->db->query($sql)->fetchAll()
        );
    }

    public function forClient(int $clientId): array
    {
        $stmt = $this->db->prepare('SELECT ' . self::COLUMNS . ' ' . self::JOINS . ' WHERE c.user_id = :id ORDER BY i.issued_at DESC');
        $stmt->execute(['id' => $clientId]);

        return array_map(
            static fn (array $row): Invoice => Invoice::fromRow($row),
            $stmt->fetchAll()
        );
    }

    public function find(int $id): ?Invoice
    {
        $stmt = $this->db->prepare('SELECT ' . self::COLUMNS . ' ' . self::JOINS . ' WHERE i.id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? Invoice::fromRow($row) : null;
    }

    public function lineItems(int $appointmentId): array
    {
        $stmt = $this->db->prepare(
            'SELECT pr.name, pr.description, ap.quantity, ap.unit_price,
                    ap.unit_price * ap.quantity AS line_total
             FROM appointment_procedures ap
             JOIN procedures pr ON pr.id = ap.procedure_id
             WHERE ap.appointment_id = :id
             ORDER BY pr.name'
        );
        $stmt->execute(['id' => $appointmentId]);

        return $stmt->fetchAll();
    }

    public function pay(int $id, string $method): string
    {
        $stmt = $this->db->prepare('SELECT status FROM invoices WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $status = $stmt->fetchColumn();

        if ($status === false) {
            return self::NOT_FOUND;
        }

        if ($status !== 'pending') {
            return self::INVALID;
        }

        $update = $this->db->prepare(
            "UPDATE invoices
             SET status = 'paid', payment_method = CAST(:method AS payment_method), paid_at = now()
             WHERE id = :id"
        );
        $update->execute(['id' => $id, 'method' => $method]);

        return self::PAID;
    }
}
