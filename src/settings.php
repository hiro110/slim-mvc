<?php
declare(strict_types=1);

// return function(){
    $dot_env = __DIR__ . '/../.env';
    if (is_readable($dot_env)) {
        $dotenv = Dotenv\Dotenv::createImmutable( __DIR__ . '/../');
        $dotenv->load();
    }

//     return $dotenv;
// };
