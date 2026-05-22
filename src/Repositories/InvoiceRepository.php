<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\Invoice;
use PDO;
use Throwable;

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

    public function all(int $clinicId): array
    {
        $stmt = $this->db->prepare('SELECT ' . self::COLUMNS . ' ' . self::JOINS . ' WHERE cu.clinic_id = :c ORDER BY i.issued_at DESC');
        $stmt->execute(['c' => $clinicId]);

        return array_map(
            static fn (array $row): Invoice => Invoice::fromRow($row),
            $stmt->fetchAll()
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

    public function find(int $id, int $clinicId): ?Invoice
    {
        $stmt = $this->db->prepare('SELECT ' . self::COLUMNS . ' ' . self::JOINS . ' WHERE i.id = :id AND cu.clinic_id = :c');
        $stmt->execute(['id' => $id, 'c' => $clinicId]);
        $row = $stmt->fetch();

        return $row ? Invoice::fromRow($row) : null;
    }

    public function appointmentForInvoice(int $appointmentId, int $vetId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT appointment_id, starts_at, reason, pet_name, species, client_name
             FROM vw_vet_weekly_schedule v
             WHERE v.appointment_id = :id AND v.vet_id = :vet AND v.status = 'completed'
               AND NOT EXISTS (SELECT 1 FROM invoices i WHERE i.appointment_id = :id)"
        );
        $stmt->execute(['id' => $appointmentId, 'vet' => $vetId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function createForAppointment(int $appointmentId, array $items): int
    {
        $this->db->beginTransaction();

        try {
            $clear = $this->db->prepare('DELETE FROM appointment_procedures WHERE appointment_id = :id');
            $clear->execute(['id' => $appointmentId]);

            $year = date('Y');
            $numberStmt = $this->db->prepare(
                "SELECT 'FV-' || :year || '-' || lpad((count(*) + 1)::text, 4, '0')
                 FROM invoices WHERE invoice_number LIKE :prefix"
            );
            $numberStmt->execute(['year' => $year, 'prefix' => 'FV-' . $year . '-%']);
            $number = (string) $numberStmt->fetchColumn();

            $invoice = $this->db->prepare(
                "INSERT INTO invoices (appointment_id, invoice_number, status)
                 VALUES (:appointment, :number, 'pending') RETURNING id"
            );
            $invoice->execute(['appointment' => $appointmentId, 'number' => $number]);
            $invoiceId = (int) $invoice->fetchColumn();

            $procedure = $this->db->prepare(
                'INSERT INTO appointment_procedures (appointment_id, procedure_id, quantity, unit_price)
                 VALUES (:appointment, :procedure, :quantity, :price)'
            );

            foreach ($items as $item) {
                $procedure->execute([
                    'appointment' => $appointmentId,
                    'procedure' => $item['procedure_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['unit_price'],
                ]);
            }

            $this->db->commit();

            return $invoiceId;
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $exception;
        }
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

    public function pay(int $id, int $clinicId, string $method): string
    {
        $stmt = $this->db->prepare(
            'SELECT i.status FROM invoices i
             JOIN appointments a ON a.id = i.appointment_id
             JOIN pets p ON p.id = a.pet_id
             JOIN clients c ON c.user_id = p.client_id
             JOIN users u ON u.id = c.user_id
             WHERE i.id = :id AND u.clinic_id = :c'
        );
        $stmt->execute(['id' => $id, 'c' => $clinicId]);
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
