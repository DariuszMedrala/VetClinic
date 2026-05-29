<?php

declare(strict_types=1);

use App\Controllers\AlertController;
use App\Controllers\AppointmentController;
use App\Controllers\AuthController;
use App\Controllers\AvailabilityController;
use App\Controllers\BillingController;
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

$router->get('/terms', [HomeController::class, 'terms']);

$router->get('/reset-password', [PasswordResetController::class, 'showRequest']);
$router->post('/reset-password', [PasswordResetController::class, 'request']);
$router->get('/reset-password/{token}', [PasswordResetController::class, 'showReset']);
$router->post('/reset-password/{token}', [PasswordResetController::class, 'reset']);

$router->get('/portal', [DashboardController::class, 'index'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('client'));

$router->get('/portal/pets/{id}', [DashboardController::class, 'pet'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('client'));

$router->get('/profile', [ProfileController::class, 'edit'])
    ->middleware(new AuthMiddleware());

$router->post('/profile', [ProfileController::class, 'update'])
    ->middleware(new AuthMiddleware());

$router->post('/profile/password', [ProfileController::class, 'updatePassword'])
    ->middleware(new AuthMiddleware());

$router->get('/dashboard', [AppointmentController::class, 'index'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('vet', 'admin'));

$router->get('/calendar', [CalendarController::class, 'index'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('vet', 'admin'));

$router->get('/availability', [AvailabilityController::class, 'edit'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('vet'));

$router->post('/availability', [AvailabilityController::class, 'save'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('vet'));

$router->post('/appointments', [CalendarController::class, 'store'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('vet', 'admin'));

$router->post('/appointments/{id}/cancel', [AppointmentController::class, 'cancel'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('vet', 'admin'));

$router->post('/appointments/{id}/complete', [AppointmentController::class, 'complete'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('vet'));

$router->get('/invoices/new/{id}', [BillingController::class, 'create'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('vet'));

$router->post('/invoices/new/{id}', [BillingController::class, 'store'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('vet'));

$router->get('/alerts', [AlertController::class, 'index'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('admin'));

$router->get('/patients', [PatientController::class, 'index'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('admin'));

$router->get('/patients/{id}', [PatientController::class, 'show'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('admin'));

$router->post('/patients', [PatientController::class, 'store'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('admin'));

$router->post('/patients/{id}/update', [PatientController::class, 'update'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('admin'));

$router->post('/patients/{id}/delete', [PatientController::class, 'destroy'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('admin'));

$router->get('/invoices', [InvoiceController::class, 'index'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('admin'));

$router->get('/invoices/{id}', [InvoiceController::class, 'show'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('admin'));

$router->post('/invoices/{id}/pay', [InvoiceController::class, 'pay'])
    ->middleware(new AuthMiddleware(), new RoleMiddleware('admin'));
