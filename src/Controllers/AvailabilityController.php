<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Services\VetAvailabilityService;

final class AvailabilityController extends Controller
{
    private VetAvailabilityService $availability;

    public function __construct()
    {
        parent::__construct();
        $this->availability = new VetAvailabilityService();
    }

    public function edit(Request $request, array $params): Response
    {
        return $this->render($this->availability->forVet((int) $this->auth->id()), null);
    }

    public function save(Request $request, array $params): Response
    {
        if (!Csrf::validate($request->input('_csrf'))) {
            return (new Response())->status(419)->html('Nieprawidłowy token CSRF.');
        }

        $days = $request->input('dni', []);
        $result = $this->availability->save((int) $this->auth->id(), is_array($days) ? $days : []);

        return $this->render($this->availability->forVet((int) $this->auth->id()), $result);
    }

    private function render(array $availability, ?array $message): Response
    {
        return $this->view('staff/dostepnosc', [
            'title' => 'VetClinic — Dostępność',
            'user' => $this->auth->user(),
            'active' => 'dostepnosc',
            'availability' => $availability,
            'message' => $message,
        ], 'app');
    }
}
