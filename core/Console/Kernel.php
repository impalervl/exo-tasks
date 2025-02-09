<?php

declare(strict_types=1);

namespace Core\Console;

use Core\Console\Contracts\CommandInterface;
use InvalidArgumentException;

class Kernel
{
    /**
     * Array mapping command names.
     *
     * @var array<string, string>
     */
    private array $commands = [];

    public function __construct()
    {
        $this->register();
    }

    /**
     * Runs a command.
     *
     * @param string $name
     * @param array<int, string> $args
     */
    public function runCommand(string $name, array $args = []): void
    {
        if (!isset($this->commands[$name])) {
            echo "Unknown command: {$name}" . PHP_EOL;
            $this->printUsage();
            return;
        }

        $commandClass = $this->commands[$name];

        /** @var CommandInterface $commandInstance */
        $commandInstance = new $commandClass();
        $commandInstance->handle($args);
    }

    /**
     * @throws InvalidArgumentException
     */
    private function register(): void
    {
        $commandMappings = require __DIR__ . '/../../routes/console.php';

        foreach ($commandMappings as $commandClass) {
            if (class_exists($commandClass) && is_subclass_of($commandClass, CommandInterface::class)) {
                $commandInstance = new $commandClass();
                $this->commands[$commandInstance->getName()] = $commandClass;
            } else {
                throw new InvalidArgumentException("Command {$commandClass} does not exist.");
            }
        }
    }

    /**
     * Prints usage instructions along with the list of registered commands.
     */
    private function printUsage(): void
    {
        echo "Usage: php cli.php <command> [arguments]" . PHP_EOL;
        echo "Available commands:" . PHP_EOL;
        foreach ($this->commands as $commandName => $commandClass) {
            echo " - {$commandName}" . PHP_EOL;
        }
    }
}
