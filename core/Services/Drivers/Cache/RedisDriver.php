<?php

declare(strict_types=1);

namespace Core\Services\Drivers\Cache;

use Core\Helpers\Config;
use Redis;
use Exception;

class RedisDriver implements CacheDriverInterface
{
    private Redis $client;

    public function __construct(array $config)
    {
        $this->client = new Redis();
        $this->connect();
    }

    private function connect(): void
    {
        try {
            $this->client->connect(Config::get('cache.redis.host'), Config::get('cache.redis.port'));
        } catch (Exception $e) {
            die("Redis connection error: " . $e->getMessage());
        }
    }

    public function set(string $key, mixed $value, int $ttl = 3600): bool
    {
        return $this->client->setex($key, $ttl, serialize($value));
    }

    public function get(string $key): mixed
    {
        $value = $this->client->get($key);
        return $value !== false ? unserialize($value) : null;
    }

    public function delete(string $key): bool
    {
        return (bool) $this->client->del($key);
    }

    public function clear(): bool
    {
        return $this->client->flushAll();
    }
}
