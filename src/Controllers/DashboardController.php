<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\ClientPortalService;
use App\Services\ClinicService;

final class DashboardController extends Controller
{
    private ClientPortalService $portal;
    private ClinicService $clinics;

    public function __construct()
    {
        parent::__construct();
        $this->portal = new ClientPortalService();
        $this->clinics = new ClinicService();
    }

    public function index(Request $request, array $params): Response
    {
        $data = $this->portal->dashboard((int) $this->auth->id());

        if ($data === null) {
            $this->session->remove('user');

            return $this->redirect('/login');
        }

        $clinic = $this->clinics->find((int) $this->auth->clinicId());

        return $this->view('portal/index', [
            'title' => 'VetClinic — Mój portal',
            'user' => $this->auth->user(),
            'active' => 'dashboard',
            'client' => $data['client'],
            'clinicName' => $clinic['name'] ?? '',
            'pets' => $data['pets'],
            'appointments' => $data['appointments'],
            'invoices' => $data['invoices'],
        ], 'app');
    }

    public function pet(Request $request, array $params): Response
    {
        $card = $this->portal->petCard((int) ($params['id'] ?? 0), (int) $this->auth->id());

        if ($card === null) {
            return $this->redirect('/portal');
        }

        return $this->view('portal/pet', [
            'title' => 'VetClinic — ' . $card['pet']->name,
            'user' => $this->auth->user(),
            'active' => 'dashboard',
            'pet' => $card['pet'],
            'vaccinations' => $card['vaccinations'],
            'history' => $card['history'],
        ], 'app');
    }
}
