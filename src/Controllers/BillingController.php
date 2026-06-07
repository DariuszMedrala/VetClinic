<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Services\BillingService;

final class BillingController extends Controller
{
    private BillingService $billing;

    public function __construct()
    {
        parent::__construct();
        $this->billing = new BillingService();
    }

    public function create(Request $request, array $params): Response
    {
        $appointmentId = (int) ($params['id'] ?? 0);
        $appointment = $this->billing->invoiceableAppointment($appointmentId, (int) $this->auth->id());

        if ($appointment === null) {
            return $this->redirect('/dashboard');
        }

        return $this->render($appointment, null);
    }

    public function store(Request $request, array $params): Response
    {
        if (!Csrf::validate($request->input('_csrf'))) {
            return (new Response())->status(419)->html('Nieprawidłowy token CSRF.');
        }

        $appointmentId = (int) ($params['id'] ?? 0);
        $vetId = (int) $this->auth->id();
        $quantities = $request->input('qty', []);
        $vaccineTypeId = (int) $request->input('vaccine_type_id', 0);

        $result = $this->billing->create(
            $appointmentId,
            $vetId,
            (int) $this->auth->clinicId(),
            is_array($quantities) ? $quantities : [],
            $vaccineTypeId > 0 ? $vaccineTypeId : null
        );

        if (!$result['ok']) {
            $appointment = $this->billing->invoiceableAppointment($appointmentId, $vetId);

            if ($appointment === null) {
                return $this->redirect('/dashboard');
            }

            return $this->render($appointment, $result['message'])->status(422);
        }

        return $this->redirect('/dashboard');
    }

    private function render(array $appointment, ?string $error): Response
    {
        return $this->view('staff/invoice-new', [
            'title' => 'VetClinic — Nowa faktura',
            'user' => $this->auth->user(),
            'active' => 'pulpit',
            'appointment' => $appointment,
            'procedures' => $this->billing->procedures((int) $this->auth->clinicId()),
            'vaccines' => $this->billing->vaccines((int) $this->auth->clinicId()),
            'error' => $error,
        ], 'app');
    }
}
