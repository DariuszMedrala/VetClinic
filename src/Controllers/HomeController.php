<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;

final class HomeController extends Controller
{
    public function index(Request $request, array $params): Response
    {
        return $this->view('home/index', [
            'title' => 'VetClinic — System zarządzania kliniką weterynaryjną',
            'bodyClass' => 'lp',
        ], 'base');
    }

    public function terms(Request $request, array $params): Response
    {
        return $this->view('pages/regulamin', [
            'title' => 'VetClinic — Regulamin i polityka prywatności',
        ], 'base');
    }
}
