<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\ClientPortalService;

final class DashboardController extends Controller
{
    private ClientPortalService $portal;

    public function __construct()
    {
        parent::__construct();
        $this->portal = new ClientPortalService();
    }

    public function index(Request $request, array $params): Response
    {
        $data = $this->portal->dashboard((int) $this->auth->id());

        if ($data === null) {
            $this->session->remove('user');

            return $this->redirect('/login');
        }

        return $this->view('portal/index', [
            'title' => 'VetClinic — Mój portal',
            'user' => $this->auth->user(),
            'client' => $data['client'],
            'pets' => $data['pets'],
            'appointments' => $data['appointments'],
            'invoices' => $data['invoices'],
        ], 'portal');
    }
}
