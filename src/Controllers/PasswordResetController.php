<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Services\PasswordResetService;

final class PasswordResetController extends Controller
{
    private PasswordResetService $resets;

    public function __construct()
    {
        parent::__construct();
        $this->resets = new PasswordResetService();
    }

    public function showRequest(Request $request, array $params): Response
    {
        return $this->view('auth/reset-request', [
            'title' => 'VetClinic — Reset hasła',
            'sent' => false,
            'error' => null,
            'email' => '',
        ], 'base');
    }

    public function request(Request $request, array $params): Response
    {
        if (!Csrf::validate($request->input('_csrf'))) {
            return (new Response())->status(419)->html('Nieprawidłowy token CSRF.');
        }

        $email = trim((string) $request->input('email', ''));

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return $this->view('auth/reset-request', [
                'title' => 'VetClinic — Reset hasła',
                'sent' => false,
                'error' => 'Podaj poprawny adres e-mail.',
                'email' => $email,
            ], 'base')->status(422);
        }

        $this->resets->request($email, $request->baseUrl());

        return $this->view('auth/reset-request', [
            'title' => 'VetClinic — Reset hasła',
            'sent' => true,
            'error' => null,
            'email' => $email,
        ], 'base');
    }

    public function showReset(Request $request, array $params): Response
    {
        $token = (string) ($params['token'] ?? '');

        return $this->view('auth/reset-form', [
            'title' => 'VetClinic — Nowe hasło',
            'token' => $token,
            'invalid' => !$this->resets->tokenValid($token),
            'done' => false,
            'error' => null,
        ], 'base');
    }

    public function reset(Request $request, array $params): Response
    {
        if (!Csrf::validate($request->input('_csrf'))) {
            return (new Response())->status(419)->html('Nieprawidłowy token CSRF.');
        }

        $token = (string) ($params['token'] ?? '');
        $password = (string) $request->input('haslo', '');
        $passwordConfirm = (string) $request->input('haslo2', '');

        $error = null;

        if (strlen($password) < 8) {
            $error = 'Hasło musi mieć co najmniej 8 znaków.';
        } elseif ($password !== $passwordConfirm) {
            $error = 'Hasła nie są identyczne.';
        }

        if ($error !== null) {
            return $this->view('auth/reset-form', [
                'title' => 'VetClinic — Nowe hasło',
                'token' => $token,
                'invalid' => false,
                'done' => false,
                'error' => $error,
            ], 'base')->status(422);
        }

        $ok = $this->resets->reset($token, $password);

        return $this->view('auth/reset-form', [
            'title' => 'VetClinic — Nowe hasło',
            'token' => $token,
            'invalid' => !$ok,
            'done' => $ok,
            'error' => null,
        ], 'base');
    }
}
