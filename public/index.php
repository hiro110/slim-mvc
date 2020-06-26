<?php
use Slim\Factory\AppFactory;

require_once( __DIR__ . '/../vendor/autoload.php');
require_once( __DIR__ . '/../src/settings.php');
require_once( __DIR__ . '/../src/dependencies.php');
$app = AppFactory::create();
require_once( __DIR__ . '/../src/middleware.php');
require_once( __DIR__ . '/../src/routes.php');
$app->run();
