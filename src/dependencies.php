<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
// use Slim\Flash\Messages;
use Slim\Csrf\Guard;
use Psr\Container\ContainerInterface;

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => $_ENV['DB_HOST'],
    'database'  => $_ENV['DB_SCHEMA'],
    'username'  => $_ENV['DB_USER'],
    'password'  => $_ENV['DB_PASS'],
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        'view' => function(ContainerInterface $c){
            $twig = Twig::create(__DIR__ . '/../templates');
            $env = $twig->getEnvironment();
            $env->addGlobal('session', $_SESSION);
            $env->addGlobal('URL_TOP', $_ENV['URL_TOP']);
            return $twig;
        },
        'logger' => function (ContainerInterface $c) {
            $logger = new Logger();
            $filerHandler = new StreamHandler(__DIR__ . '/../logs/app.log');
            $logger->pushHandler($filerHandler);
            return $logger;
        },
        'guard' => function (ContainerInterface $c) {
            $guard = new Guard;
            return $guard;
        },
        'db' => function () use ($capsule) {
            return $capsule;
        }
    ]);
};
