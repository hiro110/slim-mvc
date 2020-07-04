<?php
declare(strict_types=1);

use PDO;
use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
// use Slim\Flash\Messages;
use Slim\Csrf\Guard;

// $dotenv = Dotenv\Dotenv::createImmutable(__DIR__, '../');
// $dotenv->load();

$container = new Container();

$container->set('view',
    function() {
        $twig = Twig::create(__DIR__ . '/../templates');
        return $twig;
    }
);

$container->set('logger',
    function() {
        $logger = new Logger();
        $filerHandler = new StreamHandler(__DIR__ . '/../logs/app.log');
        $logger->pushHandler($filerHandler);
        return $logger;
    }
);

$container->set('guard',
    function() {
        $guard = new Guard;
        return $guard;
    }
);

$container->set('db',
    function() {
        // $dsn = 'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME') . ';charset=UTF8';
        // $db_user = getenv('DB_USER');
        // $db_pass = getenv('DB_PASSWORD');
        $dsn = 'mysql:host=localhost;dbname=app3;charset=UTF8';
        $db_user = 'app3';
        $db_pass = 'kQpGeMS,aRD5';

        $pdo = new PDO($dsn, $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $pdo;
    }
);

AppFactory::setContainer($container);
