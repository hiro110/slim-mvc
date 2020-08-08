<?php
use Slim\Factory\AppFactory;

if (PHP_SAPI == 'cli-server') {
    $file = __DIR__ . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false;
    }
}

require_once( __DIR__ . '/../vendor/autoload.php');
session_start();
require_once( __DIR__ . '/../src/settings.php');
require_once( __DIR__ . '/../src/dependencies.php');
$app = AppFactory::create();
require_once( __DIR__ . '/../src/middleware.php');
require_once( __DIR__ . '/../src/routes.php');
$app->run();
