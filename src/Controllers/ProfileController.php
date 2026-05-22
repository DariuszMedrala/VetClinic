<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Services\ProfileService;

final class ProfileController extends Controller
{
    private ProfileService $profiles;

    public function __construct()
    {
        parent::__construct();
        $this->profiles = new ProfileService();
    }

    public function edit(Request $request, array $params): Response
    {
        return $this->renderFor($this->auth->role(), null, null);
    }

    public function update(Request $request, array $params): Response
    {
        if (!Csrf::validate($request->input('_csrf'))) {
            return (new Response())->status(419)->html('Nieprawidłowy token CSRF.');
        }

        $userId = (int) $this->auth->id();
        $role = (string) $this->auth->role();
        $firstName = trim((string) $request->input('imie', ''));
        $lastName = trim((string) $request->input('nazwisko', ''));
        $email = trim((string) $request->input('email', ''));

        if ($firstName === '' || $lastName === '') {
            $result = ['ok' => false, 'message' => 'Imię i nazwisko są wymagane.'];
        } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $result = ['ok' => false, 'message' => 'Podaj poprawny adres e-mail.'];
        } elseif ($role === 'client') {
            $phone = trim((string) $request->input('telefon', ''));
            $result = $this->profiles->updateData($userId, $firstName, $lastName, $email, $phone !== '' ? $phone : null);
        } else {
            $result = $this->profiles->updateStaffData($userId, $firstName, $lastName, $email);

            if ($result['ok'] && $role === 'vet') {
                $title = trim((string) $request->input('tytul', '')) ?: 'Dr';
                $room = trim((string) $request->input('gabinet', ''));
                $spec = trim((string) $request->input('specjalizacja', ''));
                $this->profiles->updateVetExtra($userId, $title, $room !== '' ? $room : null, $spec !== '' ? $spec : null);
            }
        }

        if ($result['ok']) {
            $user = $this->auth->user();
            $user['name'] = $firstName . ' ' . $lastName;
            $this->session->set('user', $user);
        }

        return $this->renderFor($role, $result, null);
    }

    public function updatePassword(Request $request, array $params): Response
    {
        if (!Csrf::validate($request->input('_csrf'))) {
            return (new Response())->status(419)->html('Nieprawidłowy token CSRF.');
        }

        $userId = (int) $this->auth->id();
        $current = (string) $request->input('haslo_obecne', '');
        $new = (string) $request->input('haslo', '');
        $confirm = (string) $request->input('haslo2', '');

        if (strlen($new) < 8) {
            $result = ['ok' => false, 'message' => 'Nowe hasło musi mieć co najmniej 8 znaków.'];
        } elseif ($new !== $confirm) {
            $result = ['ok' => false, 'message' => 'Nowe hasła nie są identyczne.'];
        } else {
            $result = $this->profiles->changePassword($userId, $current, $new);
        }

        return $this->renderFor((string) $this->auth->role(), null, $result);
    }

    private function renderFor(string $role, ?array $profileMsg, ?array $passwordMsg): Response
    {
        $userId = (int) $this->auth->id();

        if ($role === 'client') {
            $client = $this->profiles->get($userId);

            if ($client === null) {
                return $this->redirect('/login');
            }

            return $this->view('portal/profile', [
                'title' => 'VetClinic — Edytuj profil',
                'user' => $this->auth->user(),
                'active' => 'profil',
                'client' => $client,
                'profileMsg' => $profileMsg,
                'passwordMsg' => $passwordMsg,
            ], 'app');
        }

        $account = $this->profiles->staffUser($userId);

        if ($account === null) {
            return $this->redirect('/login');
        }

        return $this->view('staff/profile', [
            'title' => 'VetClinic — Edytuj profil',
            'user' => $this->auth->user(),
            'active' => 'profil',
            'account' => $account,
            'vet' => $role === 'vet' ? $this->profiles->vetExtra($userId) : null,
            'isVet' => $role === 'vet',
            'profileMsg' => $profileMsg,
            'passwordMsg' => $passwordMsg,
        ], 'app');
    }
}
