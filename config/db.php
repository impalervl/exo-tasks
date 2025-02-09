<?php

declare(strict_types=1);

return [
    'default' => 'mysql',
    'mysql' => [
        'host'     => env('DB_HOST') ?: 'localhost',
        'dbname'   => env('DB_DATABASE') ?: 'my_database',
        'port'     => env('DB_PORT') ?: '3306',
        'username' => env('DB_USERNAME') ?: 'root',
        'password' => env('DB_PASSWORD') ?: 'password',
        'charset'  => 'utf8mb4',
    ]
];
