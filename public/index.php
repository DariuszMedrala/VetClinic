<?php

declare(strict_types=1);

use App\Core\Request;

define('APP_ROOT', dirname(__DIR__));

$router = require APP_ROOT . '/bootstrap.php';

$router->dispatch(Request::capture())->send();
