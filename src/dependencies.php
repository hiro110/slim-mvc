<?php
declare(strict_types=1);

use PDO;
use DI\ContainerBuilder;
// use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
// use Slim\Flash\Messages;
use Slim\Csrf\Guard;
use Psr\Container\ContainerInterface;


return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        'view' => function(ContainerInterface $c){
            $twig = Twig::create(__DIR__ . '/../templates');
            $env = $twig->getEnvironment();
            $env->addGlobal('session', $_SESSION);
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
        'db' => function (ContainerInterface $c) {
            $settings = $c->get('settings');
            $dbsettings = $settings['db'];

            // $dsn = 'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME') . ';charset=UTF8';
            // $db_user = getenv('DB_USER');
            // $db_pass = getenv('DB_PASSWORD');
            $dsn = 'mysql:host=' . $dbsettings['host'] . ';dbname=' . $dbsettings['schema'] . ';charset=UTF8';
            $db_user = $dbsettings['user'];
            $db_pass = $dbsettings['pass'];

            $pdo = new PDO($dsn, $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            return $pdo;
        }
    ]);
};
