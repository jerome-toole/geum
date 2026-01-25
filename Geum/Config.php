<?php

namespace Geum;

class Config
{
    private static ?array $config = null;

    public static function init(): void
    {
        static::load();
    }

    private static function load(): void
    {
        if (static::$config !== null) {
            return;
        }

        $path = \get_theme_file_path('config.json');
        if (! file_exists($path)) {
            static::$config = [];

            return;
        }

        $file = file_get_contents($path);
        if (empty($file)) {
            static::$config = [];

            return;
        }

        $data = json_decode($file, true);
        static::$config = $data['settings'] ?? [];
    }

    /**
     * Get a config value by key.
     *
     * @param  mixed  $default  Default value if key doesn't exist
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $value = static::$config[$key] ?? $default;

        return \apply_filters("geum/config/$key", $value);
    }
}
