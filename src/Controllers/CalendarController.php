<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Services\AppointmentService;
use App\Services\LookupService;
use DateTimeImmutable;
use Throwable;

final class CalendarController extends Controller
{
    private const DOW = [1 => 'PON', 2 => 'WT', 3 => 'ŚR', 4 => 'CZW', 5 => 'PT', 6 => 'SOB', 7 => 'NDZ'];

    private AppointmentService $appointments;
    private LookupService $lookups;

    public function __construct()
    {
        parent::__construct();
        $this->appointments = new AppointmentService();
        $this->lookups = new LookupService();
    }

    public function index(Request $request, array $params): Response
    {
        $monday = $this->mondayOf($request->query('week'));
        $sunday = $monday->modify('+6 days');
        $today = (new DateTimeImmutable('today'))->format('Y-m-d');

        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $monday->modify("+$i days");
            $days[] = [
                'date' => $day->format('Y-m-d'),
                'dow' => self::DOW[(int) $day->format('N')],
                'dom' => $day->format('j'),
                'isToday' => $day->format('Y-m-d') === $today,
            ];
        }

        $clinicId = (int) $this->auth->clinicId();
        $appointments = $this->appointments->forWeek(
            $clinicId,
            $monday->format('Y-m-d 00:00:00'),
            $monday->modify('+7 days')->format('Y-m-d 00:00:00')
        );

        return $this->view('staff/kalendarz', [
            'title' => 'VetClinic — Kalendarz',
            'user' => $this->auth->user(),
            'active' => 'kalendarz',
            'days' => $days,
            'appointments' => $appointments,
            'layout' => $this->layout($appointments),
            'weekLabel' => $monday->format('d.m') . ' – ' . $sunday->format('d.m.Y'),
            'prevWeek' => $monday->modify('-7 days')->format('Y-m-d'),
            'nextWeek' => $monday->modify('+7 days')->format('Y-m-d'),
            'todayWeek' => (new DateTimeImmutable('today'))->modify('monday this week')->format('Y-m-d'),
            'vets' => $this->lookups->vets($clinicId),
            'pets' => $this->lookups->pets($clinicId),
            'defaultDate' => $today,
        ], 'app');
    }

    public function store(Request $request, array $params): Response
    {
        if (!Csrf::validate($request->input('_csrf'))) {
            return $this->json(['ok' => false, 'message' => 'Nieprawidłowy token CSRF.'], 419);
        }

        $petId = (int) $request->input('pet_id', 0);
        $vetId = (int) $request->input('vet_id', 0);
        $date = trim((string) $request->input('date', ''));
        $time = trim((string) $request->input('time', ''));
        $duration = (int) $request->input('duration', 0);
        $reason = trim((string) $request->input('reason', ''));

        $errors = $this->validate($petId, $vetId, $date, $time, $duration, $reason);

        if ($errors !== []) {
            return $this->json(['ok' => false, 'message' => implode(' ', $errors)], 422);
        }

        $clinicId = (int) $this->auth->clinicId();

        if (!$this->lookups->petInClinic($petId, $clinicId) || !$this->lookups->vetInClinic($vetId, $clinicId)) {
            return $this->json(['ok' => false, 'message' => 'Wybrany pacjent lub lekarz nie należy do Twojej kliniki.'], 422);
        }

        $start = new DateTimeImmutable($date . ' ' . $time);
        $end = $start->modify("+$duration minutes");

        $result = $this->appointments->create(
            $petId,
            $vetId,
            $start->format('Y-m-d H:i:s'),
            $end->format('Y-m-d H:i:s'),
            $reason
        );

        return $this->json(['ok' => $result['ok'], 'message' => $result['message']], $result['status']);
    }

    private function layout(array $appointments): array
    {
        $byDay = [];
        foreach ($appointments as $appointment) {
            $byDay[$appointment->day()][] = $appointment;
        }

        $result = [];
        foreach ($byDay as $dayAppointments) {
            usort($dayAppointments, static fn ($a, $b): int => $a->startsAt <=> $b->startsAt);

            $cluster = [];
            $clusterEnd = 0;
            foreach ($dayAppointments as $appointment) {
                if ($cluster !== [] && $appointment->startsAt->getTimestamp() >= $clusterEnd) {
                    $result += $this->assignLanes($cluster);
                    $cluster = [];
                    $clusterEnd = 0;
                }

                $cluster[] = $appointment;
                $clusterEnd = max($clusterEnd, $appointment->endsAt->getTimestamp());
            }

            if ($cluster !== []) {
                $result += $this->assignLanes($cluster);
            }
        }

        return $result;
    }

    private function assignLanes(array $cluster): array
    {
        $laneEnds = [];
        $assignment = [];

        foreach ($cluster as $appointment) {
            $lane = null;
            foreach ($laneEnds as $index => $end) {
                if ($appointment->startsAt->getTimestamp() >= $end) {
                    $lane = $index;
                    break;
                }
            }

            if ($lane === null) {
                $lane = count($laneEnds);
            }

            $laneEnds[$lane] = $appointment->endsAt->getTimestamp();
            $assignment[$appointment->id] = $lane;
        }

        $lanes = count($laneEnds);
        $result = [];
        foreach ($assignment as $id => $lane) {
            $result[$id] = ['lane' => $lane, 'lanes' => $lanes];
        }

        return $result;
    }

    private function mondayOf(mixed $week): DateTimeImmutable
    {
        try {
            $base = is_string($week) && $week !== '' ? new DateTimeImmutable($week) : new DateTimeImmutable('today');
        } catch (Throwable $exception) {
            $base = new DateTimeImmutable('today');
        }

        return $base->modify('monday this week');
    }

    private function validate(int $petId, int $vetId, string $date, string $time, int $duration, string $reason): array
    {
        $errors = [];

        if ($petId <= 0 || $vetId <= 0) {
            $errors[] = 'Wybierz pacjenta i lekarza.';
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) !== 1 || preg_match('/^\d{2}:\d{2}$/', $time) !== 1) {
            $errors[] = 'Podaj poprawną datę i godzinę.';
        }

        if ($duration < 15 || $duration > 240) {
            $errors[] = 'Czas trwania musi mieścić się w zakresie 15–240 minut.';
        }

        if ($reason === '') {
            $errors[] = 'Podaj powód wizyty.';
        }

        return $errors;
    }
}
