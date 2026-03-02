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
            'title' => 'VetClinic',
        ]);
    }
}
