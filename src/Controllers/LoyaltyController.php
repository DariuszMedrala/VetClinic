<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Services\LoyaltyService;

final class LoyaltyController extends Controller
{
    private LoyaltyService $loyalty;

    public function __construct()
    {
        parent::__construct();
        $this->loyalty = new LoyaltyService();
    }

    public function index(Request $request, array $params): Response
    {
        return $this->render(null);
    }

    public function saveSettings(Request $request, array $params): Response
    {
        if (!Csrf::validate($request->input('_csrf'))) {
            return (new Response())->status(419)->html('Nieprawidłowy token CSRF.');
        }

        $msg = $this->loyalty->saveSettings(
            (int) $this->auth->clinicId(),
            trim((string) $request->input('points_per', '')),
            trim((string) $request->input('per_amount', ''))
        );

        return $this->render($msg);
    }

    public function addTier(Request $request, array $params): Response
    {
        if (!Csrf::validate($request->input('_csrf'))) {
            return (new Response())->status(419)->html('Nieprawidłowy token CSRF.');
        }

        $msg = $this->loyalty->addTier(
            (int) $this->auth->clinicId(),
            trim((string) $request->input('min_points', '')),
            trim((string) $request->input('discount_percent', ''))
        );

        return $this->render($msg);
    }

    public function deleteTier(Request $request, array $params): Response
    {
        if (!Csrf::validate($request->input('_csrf'))) {
            return (new Response())->status(419)->html('Nieprawidłowy token CSRF.');
        }

        $msg = $this->loyalty->removeTier((int) ($params['id'] ?? 0), (int) $this->auth->clinicId());

        return $this->render($msg);
    }

    private function render(?array $msg): Response
    {
        return $this->view('staff/loyalty', [
            'title' => 'VetClinic — Program lojalnościowy',
            'user' => $this->auth->user(),
            'active' => 'lojalnosc',
            'loyalty' => $this->loyalty->forClinic((int) $this->auth->clinicId()),
            'msg' => $msg,
        ], 'app');
    }
}
