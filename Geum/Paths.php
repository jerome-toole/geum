<?php

namespace Geum;

class Paths
{
    /**
     * Convert a 'relative' file path into a legitimate theme file path.
     * Supports child theme overriding.
     *
     * @param  string  $path  A 'relative' theme file path.
     * @return string The legitimate theme file path.
     */
    public static function resolve(string $path): string
    {
        $path = \get_theme_file_path($path);

        if (is_dir($path)) {
            $path = $path.'/template.php';
        }

        if (empty(pathinfo($path, PATHINFO_EXTENSION))) {
            $path = $path.'.php';
        }

        return $path;
    }

    /**
     * Retrieve a public asset URL. Supports child theme overriding.
     *
     * @param  string  $path  The asset path relative to public/.
     * @return string The full theme asset URL.
     */
    public static function assetURL(string $path = ''): string
    {
        return \get_theme_file_uri(self::assetPath($path, true));
    }

    /**
     * Retrieve a public asset path. Supports child theme overriding.
     *
     * @param  string  $path  The asset path relative to public/.
     * @param  bool  $relative  Whether to return path relative to theme root.
     * @return string The full theme asset path.
     */
    public static function assetPath(string $path = '', bool $relative = false): string
    {
        $path = "public/$path";

        return $relative ? $path : \get_theme_file_path($path);
    }
}
