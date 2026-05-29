<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\ClinicService;
use App\Services\PatientService;

final class AlertController extends Controller
{
    private PatientService $patients;
    private ClinicService $clinics;

    public function __construct()
    {
        parent::__construct();
        $this->patients = new PatientService();
        $this->clinics = new ClinicService();
    }

    public function index(Request $request, array $params): Response
    {
        $clinicId = (int) $this->auth->clinicId();

        return $this->view('staff/alerts', [
            'title' => 'VetClinic — Alerty',
            'user' => $this->auth->user(),
            'active' => 'alerty',
            'clinic' => $this->clinics->find($clinicId),
            'overdue' => $this->patients->overdueVaccinations($clinicId),
        ], 'app');
    }
}
