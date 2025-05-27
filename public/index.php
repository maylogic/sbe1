<?php

if (str_starts_with($_SERVER['REQUEST_URI'], '/apila/')) {
    $uri = substr($_SERVER['REQUEST_URI'], strlen('/apila'));
    $_SERVER['REQUEST_URI'] = $uri;
    $_SERVER['PATH_INFO'] = $uri;
    $_SERVER['PHP_SELF'] = '/index.php' . $uri;
}

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
