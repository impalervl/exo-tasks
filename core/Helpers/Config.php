<?php

declare(strict_types=1);

namespace Core\Helpers;

class Config
{
    private static array $config = [];

    public static function load(string $configDirectory): void
    {
        if (!is_dir($configDirectory)) {
            throw new \RuntimeException("Config directory not found: $configDirectory");
        }

        $files = glob($configDirectory . '/*.php');

        foreach ($files as $file) {
            $config                  = require $file;
            $filename                = pathinfo($file, PATHINFO_FILENAME);
            self::$config[$filename] = array_merge(self::$config, $config);
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $keyPart) {
            if (!is_array($value) || !array_key_exists($keyPart, $value)) {
                return $default;
            }
            $value = $value[$keyPart];
        }

        return $value;
    }

    /**
     * Set a value for a given key.
     *
     * @param string $key The key to set (supports dot notation).
     * @param mixed $value The value to set.
     */
    public static function set(string $key, mixed $value): void
    {
        $keys   = explode('.', $key);
        $config = &self::$config;

        foreach ($keys as $keyPart) {
            if (!isset($config[$keyPart]) || !is_array($config[$keyPart])) {
                $config[$keyPart] = [];
            }
            $config = &$config[$keyPart];
        }

        $config = $value;
    }
}
