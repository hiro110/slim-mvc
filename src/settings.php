<?php
declare(strict_types=1);

use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        'settings' => [
            'displayErrorDetails' => true,
            'logger' => [
            ],
            'db' => [
                'host' => $_ENV['DB_HOST'],
                'schema' => $_ENV['DB_SCHEMA'],
                'user' => $_ENV['DB_USER'],
                'pass' => $_ENV['DB_PASS'],
            ],
        ]
    ]);
};
