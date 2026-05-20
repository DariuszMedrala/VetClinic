<?php

declare(strict_types=1);

use App\Controllers\AppointmentController;
use App\Controllers\AuthController;
use App\Controllers\CalendarController;
use App\Controllers\DashboardController;
use App\Controllers\HomeController;
use App\Controllers\InvoiceController;
use App\Controllers\PasswordResetController;
use App\Controllers\PatientController;
use App\Controllers\ProfileController;
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

$router->get('/regulamin', [HomeController::class, 'terms']);

$router->get('/reset-hasla', [PasswordResetController::class, 'showRequest']);
$router->post('/reset-hasla', [PasswordResetController::class, 'request']);
$router->get('/reset-hasla/{token}', [PasswordResetController::class, 'showReset']);
$router->post('/reset-hasla/{token}', [PasswordResetController::class, 'reset']);

$router->get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('client'));

$router->get('/profil', [ProfileController::class, 'edit'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('client'));

$router->post('/profil', [ProfileController::class, 'update'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('client'));

$router->post('/profil/haslo', [ProfileController::class, 'updatePassword'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('client'));

$router->get('/pulpit', [AppointmentController::class, 'index'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('vet', 'admin'));

$router->get('/kalendarz', [CalendarController::class, 'index'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('vet', 'admin'));

$router->post('/appointments', [CalendarController::class, 'store'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('vet', 'admin'));

$router->post('/appointments/{id}/cancel', [AppointmentController::class, 'cancel'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('vet', 'admin'));

$router->get('/pacjenci', [PatientController::class, 'index'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('vet', 'admin'));

$router->get('/pacjenci/{id}', [PatientController::class, 'show'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('vet', 'admin'));

$router->post('/pacjenci', [PatientController::class, 'store'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('vet', 'admin'));

$router->post('/pacjenci/{id}/update', [PatientController::class, 'update'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('vet', 'admin'));

$router->post('/pacjenci/{id}/delete', [PatientController::class, 'destroy'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('vet', 'admin'));

$router->get('/platnosci', [InvoiceController::class, 'index'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('vet', 'admin'));

$router->get('/platnosci/{id}', [InvoiceController::class, 'show'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('vet', 'admin'));

$router->post('/platnosci/{id}/pay', [InvoiceController::class, 'pay'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('vet', 'admin'));
