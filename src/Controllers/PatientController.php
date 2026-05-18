<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Services\LookupService;
use App\Services\PatientService;

final class PatientController extends Controller
{
    private PatientService $patients;
    private LookupService $lookups;

    public function __construct()
    {
        parent::__construct();
        $this->patients = new PatientService();
        $this->lookups = new LookupService();
    }

    public function index(Request $request, array $params): Response
    {
        return $this->view('staff/pacjenci', [
            'title' => 'VetClinic — Klienci i zwierzęta',
            'user' => $this->auth->user(),
            'active' => 'pacjenci',
            'groups' => $this->patients->clientsWithPets((int) $this->auth->clinicId()),
            'species' => $this->lookups->species(),
        ], 'app');
    }

    public function show(Request $request, array $params): Response
    {
        $card = $this->patients->petCard((int) ($params['id'] ?? 0), (int) $this->auth->clinicId());

        if ($card === null) {
            return $this->redirect('/pacjenci');
        }

        return $this->view('staff/pacjent', [
            'title' => 'VetClinic — Karta pacjenta',
            'user' => $this->auth->user(),
            'active' => 'pacjenci',
            'pet' => $card['pet'],
            'vaccinations' => $card['vaccinations'],
            'history' => $card['history'],
            'species' => $this->lookups->species(),
        ], 'app');
    }

    public function store(Request $request, array $params): Response
    {
        if (!Csrf::validate($request->input('_csrf'))) {
            return $this->json(['ok' => false, 'message' => 'Nieprawidłowy token CSRF.'], 419);
        }

        $clientId = (int) $request->input('client_id', 0);
        $data = $this->petInput($request);
        $errors = $this->validate($data);

        if ($clientId <= 0) {
            $errors[] = 'Wybierz właściciela.';
        }

        if ($errors !== []) {
            return $this->json(['ok' => false, 'message' => implode(' ', $errors)], 422);
        }

        if (!$this->lookups->clientInClinic($clientId, (int) $this->auth->clinicId())) {
            return $this->json(['ok' => false, 'message' => 'Wybrany właściciel nie należy do Twojej kliniki.'], 422);
        }

        $id = $this->patients->create($clientId, $data['speciesId'], $data['name'], $data['breed'], $data['sex'], $data['birthDate'], $data['weightKg']);

        return $this->json(['ok' => true, 'id' => $id, 'message' => 'Dodano zwierzę.'], 201);
    }

    public function update(Request $request, array $params): Response
    {
        if (!Csrf::validate($request->input('_csrf'))) {
            return $this->json(['ok' => false, 'message' => 'Nieprawidłowy token CSRF.'], 419);
        }

        $id = (int) ($params['id'] ?? 0);
        $data = $this->petInput($request);
        $errors = $this->validate($data);

        if ($errors !== []) {
            return $this->json(['ok' => false, 'message' => implode(' ', $errors)], 422);
        }

        $updated = $this->patients->update($id, (int) $this->auth->clinicId(), $data['speciesId'], $data['name'], $data['breed'], $data['sex'], $data['birthDate'], $data['weightKg']);

        if (!$updated) {
            return $this->json(['ok' => false, 'message' => 'Nie znaleziono zwierzęcia.'], 404);
        }

        return $this->json(['ok' => true, 'message' => 'Zapisano zmiany.'], 200);
    }

    public function destroy(Request $request, array $params): Response
    {
        if (!Csrf::validate($request->input('_csrf'))) {
            return $this->json(['ok' => false, 'message' => 'Nieprawidłowy token CSRF.'], 419);
        }

        $deleted = $this->patients->delete((int) ($params['id'] ?? 0), (int) $this->auth->clinicId());

        if (!$deleted) {
            return $this->json(['ok' => false, 'message' => 'Nie znaleziono zwierzęcia.'], 404);
        }

        return $this->json(['ok' => true, 'message' => 'Usunięto zwierzę.'], 200);
    }

    private function petInput(Request $request): array
    {
        $breed = trim((string) $request->input('breed', ''));
        $birthDate = trim((string) $request->input('birth_date', ''));
        $weight = trim((string) $request->input('weight', ''));

        return [
            'speciesId' => (int) $request->input('species_id', 0),
            'name' => trim((string) $request->input('name', '')),
            'breed' => $breed !== '' ? $breed : null,
            'sex' => (string) $request->input('sex', 'unknown'),
            'birthDate' => $birthDate !== '' ? $birthDate : null,
            'weightKg' => $weight !== '' ? $weight : null,
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];

        if ($data['speciesId'] <= 0) {
            $errors[] = 'Wybierz gatunek.';
        }

        if ($data['name'] === '') {
            $errors[] = 'Podaj imię zwierzęcia.';
        }

        if (!in_array($data['sex'], ['male', 'female', 'unknown'], true)) {
            $errors[] = 'Wybierz prawidłową płeć.';
        }

        if ($data['birthDate'] !== null && preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['birthDate']) !== 1) {
            $errors[] = 'Podaj poprawną datę urodzenia.';
        }

        if ($data['weightKg'] !== null && (!is_numeric($data['weightKg']) || (float) $data['weightKg'] <= 0)) {
            $errors[] = 'Waga musi być liczbą dodatnią.';
        }

        return $errors;
    }
}
