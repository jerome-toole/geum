<?php

namespace Geum\WordPress;

class Colors
{
    private static ?array $config = null;

    public static function init(): void
    {
        \add_filter('wp_theme_json_data_theme', [__CLASS__, 'injectColorPalette']);
        \add_action('init', [__CLASS__, 'defineColorPaletteConstant']);
    }

    /**
     * Load theme config JSON.
     */
    public static function getConfig(): array
    {
        if (self::$config === null) {
            $path = \get_theme_file_path('assets/theme-config.json');
            $json = file_get_contents($path);
            self::$config = json_decode($json, true) ?? [];
        }

        return self::$config;
    }

    /**
     * Get colors formatted for WordPress palette.
     */
    public static function getWordPressPalette(): array
    {
        $config = self::getConfig();
        $baseColors = $config['colors']['base'] ?? [];
        $palette = [];

        foreach ($baseColors as $slug => $colorConfig) {
            // Skip colors that reference other colors (namedColor) or have no hex value
            if (empty($colorConfig['color'])) {
                continue;
            }

            $palette[] = [
                'slug' => $slug,
                'name' => $colorConfig['name'] ?? ucfirst(str_replace('-', ' ', $slug)),
                'color' => $colorConfig['color'],
            ];
        }

        return $palette;
    }

    /**
     * Inject color palette into theme.json data (WP 6.1+).
     */
    public static function injectColorPalette(\WP_Theme_JSON_Data $theme_json): \WP_Theme_JSON_Data
    {
        $palette = self::getWordPressPalette();

        if (empty($palette)) {
            return $theme_json;
        }

        $new_data = [
            'version' => 2,
            'settings' => [
                'color' => [
                    'palette' => $palette,
                ],
            ],
        ];

        return $theme_json->update_with($new_data);
    }

    /**
     * Define GEUM_COLOR_PALETTE constant for backward compatibility.
     */
    public static function defineColorPaletteConstant(): void
    {
        if (defined('GEUM_COLOR_PALETTE')) {
            return;
        }

        define('GEUM_COLOR_PALETTE', self::getWordPressPalette());
    }
}
