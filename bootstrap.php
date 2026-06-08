<?php

declare(strict_types=1);

use App\Core\Autoloader;
use App\Core\Router;
use App\Core\Session;

date_default_timezone_set('Europe/Warsaw');

$debug = filter_var(getenv('APP_DEBUG') ?: 'true', FILTER_VALIDATE_BOOLEAN);

error_reporting(E_ALL);
ini_set('display_errors', $debug ? '1' : '0');
ini_set('log_errors', '1');

set_exception_handler(static function (Throwable $exception) use ($debug): void {
    error_log((string) $exception);

    if (!headers_sent()) {
        http_response_code(500);
    }

    if ($debug) {
        echo '<pre style="white-space:pre-wrap;padding:20px;font-family:monospace;">'
            . htmlspecialchars((string) $exception, ENT_QUOTES, 'UTF-8') . '</pre>';

        return;
    }

    $page = APP_ROOT . '/src/Views/errors/500.php';
    echo is_file($page) ? file_get_contents($page) : 'Wystąpił błąd serwera.';
});

require APP_ROOT . '/src/Core/Autoloader.php';

(new Autoloader('App', APP_ROOT . '/src'))->register();

require APP_ROOT . '/src/helpers.php';

(new Session())->start();

$router = new Router();

require APP_ROOT . '/src/routes.php';

return $router;
