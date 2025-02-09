<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap/app.php';

use Core\Console\Kernel;

$kernel = new Kernel();

if ($argc < 2) {
    echo "Usage: php cli.php <command> [arguments]" . PHP_EOL;
    exit(1);
}

$commandName = $argv[1];
$arguments   = array_slice($argv, 2);

$kernel->runCommand($commandName, $arguments);
