<?php

declare(strict_types=1);

namespace App\Services\ABTest\Storages;

use Core\Services\CacheService;

class CacheStorage implements ABTestStorageInterface
{
    private CacheService $cache;

    public function __construct()
    {
        $this->cache = CacheService::getInstance();
    }

    public function getDesignCounts(int $promotionId): array
    {
        $cacheKey     = "promotion_{$promotionId}_design_counts";
        $cachedCounts = $this->cache->get($cacheKey);

        return $cachedCounts ?? [];
    }

    public function incrementDesignCount(int $promotionId, int $designId): void
    {
        $cacheKey = "promotion_{$promotionId}_design_counts";
        $counts   = $this->cache->get($cacheKey) ?? [];

        $counts[$designId] = ($counts[$designId] ?? 0) + 1;

        $this->cache->set($cacheKey, $counts);
    }
}
