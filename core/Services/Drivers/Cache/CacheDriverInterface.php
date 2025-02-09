<?php

declare(strict_types=1);

namespace Core\Services\Drivers\Cache;

interface CacheDriverInterface
{
    public function set(string $key, mixed $value, int $ttl = 3600): bool;

    public function get(string $key): mixed;

    public function delete(string $key): bool;

    public function clear(): bool;
}
