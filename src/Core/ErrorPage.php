<?php

declare(strict_types=1);

namespace App\Core;

final class ErrorPage
{
    private const PAGES = [
        400 => ['Nieprawidłowe żądanie', 'Serwer nie zrozumiał Twojego żądania. Sprawdź adres lub spróbuj ponownie.'],
        403 => ['Brak dostępu', 'Twoja rola nie pozwala na otwarcie tej strony. Skontaktuj się z administratorem, jeśli uważasz, że to pomyłka.'],
        404 => ['Nie znaleziono strony', 'Strona, której szukasz, nie istnieje lub została przeniesiona.'],
        500 => ['Coś poszło nie tak', 'Wystąpił nieoczekiwany błąd serwera. Spróbuj ponownie za chwilę — jeśli problem się powtarza, skontaktuj się z administratorem.'],
    ];

    public static function render(int $code, bool $json = false): Response
    {
        [$heading, $message] = self::PAGES[$code] ?? self::PAGES[500];

        if ($json) {
            return (new Response())->status($code)->json(['error' => $heading]);
        }

        $view = new View(APP_ROOT . '/src/Views');
        $html = $view->render('errors/error', [
            'title' => 'VetClinic — ' . $heading,
            'code' => $code,
            'heading' => $heading,
            'message' => $message,
        ], 'base');

        return (new Response())->status($code)->html($html);
    }
}
