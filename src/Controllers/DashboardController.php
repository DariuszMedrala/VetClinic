<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\ClientPortalService;
use App\Services\ClinicService;
use App\Services\LoyaltyService;

final class DashboardController extends Controller
{
    private ClientPortalService $portal;
    private ClinicService $clinics;
    private LoyaltyService $loyalty;

    public function __construct()
    {
        parent::__construct();
        $this->portal = new ClientPortalService();
        $this->clinics = new ClinicService();
        $this->loyalty = new LoyaltyService();
    }

    public function index(Request $request, array $params): Response
    {
        $data = $this->portal->dashboard((int) $this->auth->id());

        if ($data === null) {
            $this->session->remove('user');

            return $this->redirect('/login');
        }

        $clinicId = (int) $this->auth->clinicId();
        $clinic = $this->clinics->find($clinicId);

        return $this->view('portal/index', [
            'title' => 'VetClinic — Mój portal',
            'user' => $this->auth->user(),
            'active' => 'dashboard',
            'client' => $data['client'],
            'clinicName' => $clinic['name'] ?? '',
            'loyaltyDiscount' => $this->loyalty->discountForPoints($clinicId, $data['client']->loyaltyPoints),
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
