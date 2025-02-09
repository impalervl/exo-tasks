<?php

declare(strict_types=1);

use Core\Helpers\Config;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';


$envPath = __DIR__ . '/../.env';

if (!file_exists($envPath)) {
    echo 'The .env file does not exist at: ' . $envPath;
    exit;
}

$dotenv = Dotenv::createImmutable(__DIR__ . '/../', '.env');
$dotenv->load();

Config::load(__DIR__ . '/../config');
