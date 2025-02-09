<?php

declare(strict_types=1);

namespace Core\Helpers;

class Cookie
{
    public static function set(string $name, string $value): void
    {
        setcookie($name, $value, time() + (30 * 24 * 60 * 60), '/', '', true, true);
        $_COOKIE[$name] = $value;
    }

    public static function get(string $name): ?string
    {
        if (isset($_COOKIE[$name])) {
            return $_COOKIE[$name];
        }

        return null;
    }

    public static function delete(string $name): void
    {
        setcookie($name, '', time() - 3600, '/', '', true, true);
        unset($_COOKIE[$name]);
    }
}
