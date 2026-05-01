<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Services\AppointmentService;
use App\Services\StatsService;

final class AppointmentController extends Controller
{
    private AppointmentService $appointments;
    private StatsService $stats;

    public function __construct()
    {
        parent::__construct();
        $this->appointments = new AppointmentService();
        $this->stats = new StatsService();
    }

    public function index(Request $request, array $params): Response
    {
        return $this->view('staff/pulpit', [
            'title' => 'VetClinic — Pulpit',
            'user' => $this->auth->user(),
            'appointments' => $this->appointments->upcoming(),
            'stats' => $this->stats->forDashboard(),
        ], 'app');
    }

    public function cancel(Request $request, array $params): Response
    {
        if (!Csrf::validate($request->input('_csrf'))) {
            return $this->json(['ok' => false, 'message' => 'Nieprawidłowy token CSRF.'], 419);
        }

        $id = (int) ($params['id'] ?? 0);
        $result = $this->appointments->cancel($id);

        return $this->json(['ok' => $result['ok'], 'message' => $result['message']], $result['status']);
    }
}
