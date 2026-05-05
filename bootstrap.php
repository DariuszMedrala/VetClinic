<?php

declare(strict_types=1);

use App\Core\Autoloader;
use App\Core\Router;
use App\Core\Session;

date_default_timezone_set('Europe/Warsaw');

require APP_ROOT . '/src/Core/Autoloader.php';

(new Autoloader('App', APP_ROOT . '/src'))->register();

require APP_ROOT . '/src/helpers.php';

(new Session())->start();

$router = new Router();

require APP_ROOT . '/src/routes.php';

return $router;
