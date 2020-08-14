<?php
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;

if (PHP_SAPI == 'cli-server') {
    $file = __DIR__ . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false;
    }
}

require_once( __DIR__ . '/../vendor/autoload.php');

$dot_env = __DIR__. '/../.env';
if (is_readable($dot_env)) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}

$containerBuilder = new ContainerBuilder();

session_start();
$settings = require_once( __DIR__ . '/../src/settings.php');
$settings($containerBuilder);

$dependencies = require_once( __DIR__ . '/../src/dependencies.php');
$dependencies($containerBuilder);

$container = $containerBuilder->build();

AppFactory::setContainer($container);
$app = AppFactory::create();

require_once( __DIR__ . '/../src/middleware.php');
require_once( __DIR__ . '/../src/routes.php');
$app->run();
