<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Services\CatalogService;

final class CatalogController extends Controller
{
    private CatalogService $catalog;

    public function __construct()
    {
        parent::__construct();
        $this->catalog = new CatalogService();
    }

    public function index(Request $request, array $params): Response
    {
        return $this->render(null);
    }

    public function add(Request $request, array $params): Response
    {
        if (!Csrf::validate($request->input('_csrf'))) {
            return (new Response())->status(419)->html('Nieprawidłowy token CSRF.');
        }

        $clinicId = (int) $this->auth->clinicId();
        $name = trim((string) $request->input('name', ''));

        $msg = match ((string) ($params['type'] ?? '')) {
            'reasons' => $this->catalog->addReason($clinicId, $name),
            'vaccines' => $this->catalog->addVaccine($clinicId, $name, trim((string) $request->input('price', '')), trim((string) $request->input('validity_months', ''))),
            'treatments' => $this->catalog->addProcedure($clinicId, $name, trim((string) $request->input('price', ''))),
            default => ['ok' => false, 'message' => 'Nieznana kategoria.'],
        };

        return $this->render($msg);
    }

    public function remove(Request $request, array $params): Response
    {
        if (!Csrf::validate($request->input('_csrf'))) {
            return (new Response())->status(419)->html('Nieprawidłowy token CSRF.');
        }

        $msg = $this->catalog->remove(
            (string) ($params['type'] ?? ''),
            (int) ($params['id'] ?? 0),
            (int) $this->auth->clinicId()
        );

        return $this->render($msg);
    }

    private function render(?array $msg): Response
    {
        return $this->view('staff/catalog', [
            'title' => 'VetClinic — Katalog',
            'user' => $this->auth->user(),
            'active' => 'katalog',
            'catalog' => $this->catalog->forClinic((int) $this->auth->clinicId()),
            'msg' => $msg,
        ], 'app');
    }
}
