<?php

declare(strict_types=1);

use App\Controllers\AppointmentController;
use App\Controllers\AuthController;
use App\Controllers\CalendarController;
use App\Controllers\DashboardController;
use App\Controllers\HomeController;
use App\Core\Router;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

/** @var Router $router */

$router->get('/', [HomeController::class, 'index']);

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->post('/logout', [AuthController::class, 'logout'])->middleware(new AuthMiddleware());

$router->get('/dashboard', [DashboardController::class, 'index'])->middleware(new AuthMiddleware());

$router->get('/pulpit', [AppointmentController::class, 'index'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('vet', 'admin'));

$router->get('/kalendarz', [CalendarController::class, 'index'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('vet', 'admin'));

$router->post('/appointments', [CalendarController::class, 'store'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('vet', 'admin'));

$router->post('/appointments/{id}/cancel', [AppointmentController::class, 'cancel'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('vet', 'admin'));
