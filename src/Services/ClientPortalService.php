<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\AppointmentRepository;
use App\Repositories\ClientRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\PetRepository;

final class ClientPortalService
{
    private ClientRepository $clients;
    private PetRepository $pets;
    private AppointmentRepository $appointments;
    private InvoiceRepository $invoices;

    public function __construct()
    {
        $this->clients = new ClientRepository();
        $this->pets = new PetRepository();
        $this->appointments = new AppointmentRepository();
        $this->invoices = new InvoiceRepository();
    }

    public function dashboard(int $userId): ?array
    {
        $client = $this->clients->find($userId);

        if ($client === null) {
            return null;
        }

        return [
            'client' => $client,
            'pets' => $this->pets->forClient($userId),
            'appointments' => $this->appointments->upcomingForClient($userId),
            'invoices' => $this->invoices->forClient($userId),
        ];
    }
}
