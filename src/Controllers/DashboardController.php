<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;

final class DashboardController extends Controller
{
    public function index(Request $request, array $params): Response
    {
        return $this->view('dashboard/index', [
            'title' => 'VetClinic — Pulpit',
            'user' => $this->auth->user(),
        ], 'base');
    }
}
