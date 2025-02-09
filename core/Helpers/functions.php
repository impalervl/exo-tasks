<?php

declare(strict_types=1);

if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        $value = getenv($key);

        return $value ?: $default;
    }
}
