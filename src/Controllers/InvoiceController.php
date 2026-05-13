<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Services\InvoiceService;

final class InvoiceController extends Controller
{
    private InvoiceService $invoices;

    public function __construct()
    {
        parent::__construct();
        $this->invoices = new InvoiceService();
    }

    public function index(Request $request, array $params): Response
    {
        return $this->view('staff/platnosci', [
            'title' => 'VetClinic — Płatności',
            'user' => $this->auth->user(),
            'active' => 'platnosci',
            'invoices' => $this->invoices->all(),
        ], 'app');
    }

    public function show(Request $request, array $params): Response
    {
        $detail = $this->invoices->detail((int) ($params['id'] ?? 0));

        if ($detail === null) {
            return $this->redirect('/platnosci');
        }

        return $this->view('staff/faktura', [
            'title' => 'VetClinic — Faktura',
            'user' => $this->auth->user(),
            'active' => 'platnosci',
            'invoice' => $detail['invoice'],
            'items' => $detail['items'],
        ], 'app');
    }

    public function pay(Request $request, array $params): Response
    {
        if (!Csrf::validate($request->input('_csrf'))) {
            return $this->json(['ok' => false, 'message' => 'Nieprawidłowy token CSRF.'], 419);
        }

        $method = (string) $request->input('payment_method', '');

        if (!in_array($method, ['card', 'cash', 'insurance'], true)) {
            return $this->json(['ok' => false, 'message' => 'Wybierz metodę płatności.'], 422);
        }

        $result = $this->invoices->pay((int) ($params['id'] ?? 0), $method);

        return $this->json(['ok' => $result['ok'], 'message' => $result['message']], $result['status']);
    }
}
