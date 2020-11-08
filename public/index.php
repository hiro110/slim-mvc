<?php

use App\Application\Handlers\HttpErrorHandler;
use App\Application\ResponseEmitter\ResponseEmitter;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;
use App\Middlewares\SessionMiddleware;

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

$settings = require_once( __DIR__ . '/../src/settings.php');
$settings($containerBuilder);

$dependencies = require_once( __DIR__ . '/../src/dependencies.php');
$dependencies($containerBuilder);

$container = $containerBuilder->build();

AppFactory::setContainer($container);
$app = AppFactory::create();
$callableResolver = $app->getCallableResolver();
$app->add(new SessionMiddleware);

require_once( __DIR__ . '/../src/middleware.php');
require_once( __DIR__ . '/../src/routes.php');

$displayErrorDetails = $container->get('settings')['displayErrorDetails'];

$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

// $responseFactory = $app->getResponseFactory();
// $errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);
$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, true, true);
// $errorMiddleware->setDefaultErrorHandler($errorHandler);

$response = $app->handle($request);
$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);
