<?php

declare(strict_types=1);

namespace Core\Services;

use Core\Helpers\Config;
use Core\Services\Drivers\Cache\RedisDriver;
use InvalidArgumentException;

class CacheService
{
    private static ?self $instance = null;
    private object $driver;

    private function __construct()
    {
        $config = Config::get('cache');
        $this->resolveDriver($config);
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function set(string $key, mixed $value, int $ttl = 3600): bool
    {
        return $this->driver->set($key, $value, $ttl);
    }

    public function get(string $key): mixed
    {
        return $this->driver->get($key);
    }

    public function delete(string $key): bool
    {
        return $this->driver->delete($key);
    }

    public function clear(): bool
    {
        return $this->driver->clear();
    }

    private function resolveDriver(array $config): void
    {
        $driver = $config['default'] ?? '';

        $this->driver = match ($driver) {
            'redis' => new RedisDriver($config),
            default => throw new InvalidArgumentException("Unsupported cache driver: $driver"),
        };
    }
}
