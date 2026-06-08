<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Models\User;
use App\Services\AuthService;
use App\Services\ClinicService;

final class AuthController extends Controller
{
    private AuthService $authService;
    private ClinicService $clinics;

    public function __construct()
    {
        parent::__construct();
        $this->authService = new AuthService();
        $this->clinics = new ClinicService();
    }

    public function showLogin(Request $request, array $params): Response
    {
        return $this->view('auth/login', [
            'title' => 'VetClinic — Logowanie',
            'error' => null,
        ], 'base');
    }

    public function login(Request $request, array $params): Response
    {
        if (!Csrf::validate($request->input('_csrf'))) {
            return (new Response())->status(419)->html('Nieprawidłowy token CSRF.');
        }

        $ip = $request->ip();

        if ($this->authService->isLockedOut($ip)) {
            return $this->view('auth/login', [
                'title' => 'VetClinic — Logowanie',
                'error' => 'Zbyt wiele nieudanych prób logowania. Odczekaj ' . $this->authService->lockoutMinutes() . ' minut i spróbuj ponownie.',
            ], 'base')->status(429);
        }

        $email = trim((string) $request->input('email', ''));
        $password = (string) $request->input('haslo', '');
        $user = $this->authService->attempt($email, $password);

        if ($user === null) {
            $this->authService->recordFailedAttempt($ip);

            return $this->view('auth/login', [
                'title' => 'VetClinic — Logowanie',
                'error' => 'Nieprawidłowy e-mail lub hasło.',
            ], 'base')->status(401);
        }

        $this->authService->clearAttempts($ip);
        $this->storeSession($user);

        return $this->redirect($this->homeFor($user->role));
    }

    public function showRegister(Request $request, array $params): Response
    {
        return $this->view('auth/register', [
            'title' => 'VetClinic — Rejestracja',
            'errors' => [],
            'old' => [],
            'clinics' => $this->clinics->all(),
        ], 'base');
    }

    public function register(Request $request, array $params): Response
    {
        if (!Csrf::validate($request->input('_csrf'))) {
            return (new Response())->status(419)->html('Nieprawidłowy token CSRF.');
        }

        $firstName = trim((string) $request->input('imie', ''));
        $lastName = trim((string) $request->input('nazwisko', ''));
        $email = trim((string) $request->input('email', ''));
        $password = (string) $request->input('haslo', '');
        $passwordConfirm = (string) $request->input('haslo2', '');
        $role = (string) $request->input('rola', 'klient');
        $clinicId = (int) $request->input('klinika_id', 0);
        $clinicName = trim((string) $request->input('klinika_nazwa', ''));
        $clinicAddress = trim((string) $request->input('klinika_adres', ''));
        $joinCode = trim((string) $request->input($role === 'recepcja' ? 'klinika_haslo_new' : 'klinika_haslo', ''));
        $accepted = $request->input('regulamin') !== null;

        $errors = $this->validateRegistration($firstName, $lastName, $email, $password, $passwordConfirm, $role, $clinicId, $clinicName, $clinicAddress, $joinCode, $accepted);

        if ($errors !== []) {
            return $this->view('auth/register', [
                'title' => 'VetClinic — Rejestracja',
                'errors' => $errors,
                'old' => ['imie' => $firstName, 'nazwisko' => $lastName, 'email' => $email, 'rola' => $role, 'klinika_id' => $clinicId, 'klinika_nazwa' => $clinicName, 'klinika_adres' => $clinicAddress],
                'clinics' => $this->clinics->all(),
            ], 'base')->status(422);
        }

        $user = $this->authService->register($firstName, $lastName, $email, $password, $role, $clinicId, $clinicName, $clinicAddress, $joinCode);

        $this->storeSession($user);

        return $this->redirect($this->homeFor($user->role));
    }

    public function logout(Request $request, array $params): Response
    {
        if (!Csrf::validate($request->input('_csrf'))) {
            return (new Response())->status(419)->html('Nieprawidłowy token CSRF.');
        }

        $this->session->destroy();

        return $this->redirect('/');
    }

    private function storeSession(User $user): void
    {
        $this->session->regenerate();
        $this->session->set('user', [
            'id' => $user->id,
            'name' => $user->fullName(),
            'role' => $user->role,
            'clinic_id' => $user->clinicId,
        ]);
    }

    private function homeFor(string $role): string
    {
        return in_array($role, ['vet', 'admin'], true) ? '/dashboard' : '/portal';
    }

    private function validateRegistration(string $firstName, string $lastName, string $email, string $password, string $passwordConfirm, string $role, int $clinicId, string $clinicName, string $clinicAddress, string $joinCode, bool $accepted): array
    {
        $errors = [];

        if ($firstName === '' || $lastName === '') {
            $errors[] = 'Imię i nazwisko są wymagane.';
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors[] = 'Podaj poprawny adres e-mail.';
        }

        if (strlen($password) < 8) {
            $errors[] = 'Hasło musi mieć co najmniej 8 znaków.';
        }

        if ($password !== $passwordConfirm) {
            $errors[] = 'Hasła nie są identyczne.';
        }

        if (!in_array($role, ['lekarz', 'recepcja', 'klient'], true)) {
            $errors[] = 'Wybierz prawidłową rolę.';
        } elseif ($role === 'recepcja') {
            if ($clinicName === '' || $clinicAddress === '') {
                $errors[] = 'Podaj nazwę i adres kliniki.';
            }
            if (strlen($joinCode) < 4) {
                $errors[] = 'Ustaw hasło dołączeniowe kliniki (min. 4 znaki).';
            }
        } elseif ($clinicId <= 0 || !$this->clinics->exists($clinicId)) {
            $errors[] = 'Wybierz klinikę z listy.';
        } elseif (!$this->clinics->verifyJoinCode($clinicId, $joinCode)) {
            $errors[] = 'Nieprawidłowe hasło dołączeniowe wybranej kliniki.';
        }

        if (!$accepted) {
            $errors[] = 'Musisz zaakceptować regulamin.';
        }

        if ($errors === [] && $this->authService->emailTaken($email)) {
            $errors[] = 'Konto z tym adresem e-mail już istnieje.';
        }

        return $errors;
    }
}
