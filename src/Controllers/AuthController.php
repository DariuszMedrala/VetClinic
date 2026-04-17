<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;

final class AuthController extends Controller
{
    private AuthService $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = new AuthService();
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

        $email = trim((string) $request->input('email', ''));
        $password = (string) $request->input('haslo', '');
        $user = $this->auth->attempt($email, $password);

        if ($user === null) {
            return $this->view('auth/login', [
                'title' => 'VetClinic — Logowanie',
                'error' => 'Nieprawidłowy e-mail lub hasło.',
            ], 'base')->status(401);
        }

        $this->session->set('user', [
            'id' => $user->id,
            'name' => $user->fullName(),
            'role' => $user->role,
        ]);

        return $this->redirect('/dashboard');
    }

    public function showRegister(Request $request, array $params): Response
    {
        return $this->view('auth/register', [
            'title' => 'VetClinic — Rejestracja',
            'errors' => [],
            'old' => [],
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
        $role = (string) $request->input('rola', 'lekarz');
        $accepted = $request->input('regulamin') !== null;

        $errors = $this->validateRegistration($firstName, $lastName, $email, $password, $passwordConfirm, $role, $accepted);

        if ($errors !== []) {
            return $this->view('auth/register', [
                'title' => 'VetClinic — Rejestracja',
                'errors' => $errors,
                'old' => ['imie' => $firstName, 'nazwisko' => $lastName, 'email' => $email, 'rola' => $role],
            ], 'base')->status(422);
        }

        $user = $this->auth->register($firstName, $lastName, $email, $password, $role);

        $this->session->set('user', [
            'id' => $user->id,
            'name' => $user->fullName(),
            'role' => $user->role,
        ]);

        return $this->redirect('/dashboard');
    }

    public function logout(Request $request, array $params): Response
    {
        if (!Csrf::validate($request->input('_csrf'))) {
            return (new Response())->status(419)->html('Nieprawidłowy token CSRF.');
        }

        $this->session->remove('user');

        return $this->redirect('/');
    }

    private function validateRegistration(string $firstName, string $lastName, string $email, string $password, string $passwordConfirm, string $role, bool $accepted): array
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

        if (!in_array($role, ['lekarz', 'recepcja'], true)) {
            $errors[] = 'Wybierz prawidłową rolę.';
        }

        if (!$accepted) {
            $errors[] = 'Musisz zaakceptować regulamin.';
        }

        if ($errors === [] && $this->auth->emailTaken($email)) {
            $errors[] = 'Konto z tym adresem e-mail już istnieje.';
        }

        return $errors;
    }
}
