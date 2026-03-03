<?php

namespace Geum;

class Module
{
    protected static array $modules = [];

    public static function init(): void
    {
        $disabled = apply_filters('geum/modules/disabled', []);
        $paths = glob(\get_theme_file_path('Theme/Modules/*/module.php'));

        foreach ($paths as $path) {
            $name = basename(dirname($path));

            if (in_array($name, $disabled)) {
                continue;
            }

            $class = "Theme\\Modules\\{$name}\\{$name}Module";

            if (! class_exists($class, false)) {
                require_once $path;
            }

            if (class_exists($class) && method_exists($class, 'init')) {
                $class::init();
                self::$modules[] = $name;
            }
        }
    }

    public static function getLoaded(): array
    {
        return self::$modules;
    }
}
