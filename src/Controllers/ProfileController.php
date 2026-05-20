<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Models\Client;
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
        $client = $this->profiles->get((int) $this->auth->id());

        if ($client === null) {
            return $this->redirect('/dashboard');
        }

        return $this->render($client, null, null);
    }

    public function update(Request $request, array $params): Response
    {
        if (!Csrf::validate($request->input('_csrf'))) {
            return (new Response())->status(419)->html('Nieprawidłowy token CSRF.');
        }

        $userId = (int) $this->auth->id();
        $firstName = trim((string) $request->input('imie', ''));
        $lastName = trim((string) $request->input('nazwisko', ''));
        $email = trim((string) $request->input('email', ''));
        $phone = trim((string) $request->input('telefon', ''));

        $error = null;

        if ($firstName === '' || $lastName === '') {
            $error = 'Imię i nazwisko są wymagane.';
        } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $error = 'Podaj poprawny adres e-mail.';
        }

        if ($error !== null) {
            $result = ['ok' => false, 'message' => $error];
        } else {
            $result = $this->profiles->updateData($userId, $firstName, $lastName, $email, $phone !== '' ? $phone : null);

            if ($result['ok']) {
                $user = $this->auth->user();
                $user['name'] = $firstName . ' ' . $lastName;
                $this->session->set('user', $user);
            }
        }

        return $this->render($this->profiles->get($userId), $result, null);
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

        return $this->render($this->profiles->get($userId), null, $result);
    }

    private function render(Client $client, ?array $profileMsg, ?array $passwordMsg): Response
    {
        return $this->view('portal/profil', [
            'title' => 'VetClinic — Edytuj profil',
            'user' => $this->auth->user(),
            'active' => 'profil',
            'client' => $client,
            'profileMsg' => $profileMsg,
            'passwordMsg' => $passwordMsg,
        ], 'app');
    }
}
