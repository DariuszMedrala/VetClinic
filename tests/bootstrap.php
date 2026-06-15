<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

require APP_ROOT . '/src/Core/Autoloader.php';

(new App\Core\Autoloader('App', APP_ROOT . '/src'))->register();

require APP_ROOT . '/src/helpers.php';
