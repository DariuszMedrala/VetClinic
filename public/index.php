<?php

declare(strict_types=1);

/*
 * VetClinic — Front Controller (jedyny punkt wejścia aplikacji).
 *
 * nginx kieruje tu każde żądanie (try_files ... /index.php). Dzięki temu,
 * że webrootem jest katalog public/, reszta kodu (src/, konfiguracja, .env)
 * jest niedostępna z przeglądarki — to fundament bezpieczeństwa aplikacji.
 *
 * W Etapie 1 podłączymy tu autoloader, kontener konfiguracji i Router MVC.
 * Na razie jest to bootstrap potwierdzający, że nowa struktura działa.
 */

define('APP_ROOT', dirname(__DIR__));

// Szybki sanity-check połączenia ze zmiennymi środowiskowymi (bez sekretów).
$dbHost = getenv('DB_HOST') ?: 'niezdefiniowany';
$dbPort = getenv('DB_PORT') ?: 'niezdefiniowany';
$dbName = getenv('POSTGRES_DB') ?: 'niezdefiniowana';

http_response_code(200);
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VetClinic — środowisko gotowe</title>
  <style>
    body { font-family: system-ui, sans-serif; margin: 0; display: grid;
           place-items: center; min-height: 100vh; background: #0f766e; color: #fff; }
    .card { background: #fff; color: #134e4a; padding: 2.5rem 3rem; border-radius: 16px;
            box-shadow: 0 20px 50px rgba(0,0,0,.25); max-width: 440px; }
    h1 { margin: 0 0 .25rem; font-size: 1.6rem; }
    p { margin: .35rem 0; color: #475569; }
    code { background: #f1f5f9; padding: 2px 6px; border-radius: 6px; }
    .ok { color: #0f766e; font-weight: 700; }
  </style>
</head>
<body>
  <main class="card">
    <h1>🐾 VetClinic</h1>
    <p class="ok">Front controller działa — webroot = <code>public/</code></p>
    <p>Baza: <code><?= htmlspecialchars($dbName) ?></code>
       @ <code><?= htmlspecialchars($dbHost) ?>:<?= htmlspecialchars($dbPort) ?></code></p>
    <p>Etap 1 podłączy tu Router MVC.</p>
  </main>
</body>
</html>
