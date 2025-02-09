<?php

declare(strict_types=1);

namespace App\Services\ABTest\Storages;

use Core\Helpers\Config;

class ABTestStorageFactory
{
    public static function getStorage(): ABTestStorageInterface
    {
        $randomizer = Config::get('ab-test.storage');

        return match ($randomizer) {
            'db'    => new DatabaseStorage(),
            'cache' => new CacheStorage(),
            default => new CacheStorage(),
        };
    }
}
