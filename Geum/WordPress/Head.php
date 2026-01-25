<?php

namespace Geum\WordPress;

use Geum\Config;

/**
 * Handles any non-cleanup <head> functionality.
 *
 * @see Cleanup.php
 */
class Head
{
    public static function init(): void
    {
        \add_action('wp_head', [__CLASS__, 'metaElements'], 0);
        \add_action('wp_head', [__CLASS__, 'linkElements'], 0);
        \add_action('wp_head', [__CLASS__, 'javascriptDetection'], 0);

        \add_filter('Geum\wordpress\head\meta', [__CLASS__, 'addThemeColorMeta']);
        \add_filter('Geum\wordpress\head\links', [__CLASS__, 'addWebmanifestLink']);
        \add_filter('Geum\wordpress\head\links', [__CLASS__, 'preloadThemeAssets']);
    }

    /**
     * Output <meta> elements in the <head>.
     *
     * Outputs meta elements from a filtered array of attribute arrays. Includes several defaults.
     */
    public static function metaElements(): void
    {
        $meta_items = \apply_filters('Geum\wordpress\head\meta', [
            [
                'charset' => \get_bloginfo('charset'),
            ],
            [
                'name' => 'viewport',
                'content' => 'width=device-width, initial-scale=1, viewport-fit=cover',
            ],
        ]);

        foreach ($meta_items as $meta_item) {
            echo '<meta '.attributes($meta_item).">\n";
        }
    }

    /**
     * Add theme color <meta> tag to the head.
     *
     * Hooks into the `Geum\wordpress\head\meta` filter to add the site manifest theme color value.
     *
     * @param  array  $meta  An array of meta attribute arrays.
     * @return array The filtered meta array, with theme color data appended.
     */
    public static function addThemeColorMeta(array $meta): array
    {
        $manifest = \Geum\Asset::decodedContent('static/site.webmanifest');

        if (! empty($manifest['theme_color'])) {
            $meta[] = [
                'name' => 'theme-color',
                'content' => $manifest['theme_color'],
            ];
        }

        return $meta;
    }

    /**
     * Output <link> elements in the <head>.
     *
     * Outputs link elements from a filtered array of attribute arrays. Includes the theme web manifest by default.
     */
    public static function linkElements(): void
    {
        $links = \apply_filters('Geum\wordpress\head\links', []);

        foreach ($links as $link) {
            if (! empty($link['href'])) {
                echo '<link '.attributes($link).">\n";
            }
        }
    }

    /**
     * Add webmanifest link to the head for PWA support.
     *
     * Hooks into the `Geum\wordpress\head\links` filter to add webmanifest.
     *
     * @see assets/static/site.webmanifest
     *
     * @param  array  $links  An array of link attribute arrays.
     * @return array The links array with the webmanifest added
     */
    public static function addWebmanifestLink(array $links): array
    {
        if (! Config::get('enable_webmanifest', false)) {
            return $links;
        }

        $links[] = [
            'rel' => 'manifest',
            'href' => \Geum\Asset::URL('static/site.webmanifest'),
            'crossorigin' => 'use-credentials',
        ];

        return $links;
    }

    /**
     * Add preload <link> tags to the head.
     *
     * Hooks into the `Geum\wordpress\head\links` filter to add preload assets.
     * The 'rel' attribute for these assets can still be overriden with another value, e.g. 'prefetch'.
     *
     * @see /config.php
     *
     * @param  array  $links  An array of link attribute arrays.
     * @return array The filtered links array, with any preloads appended.
     */
    public static function preloadThemeAssets(array $links): array
    {
        $preload_assets = \apply_filters('geum/wordpress/head/preload_assets', []);
        if (empty($preload_assets) || ! is_array($preload_assets)) {
            return $links;
        }

        $defaults = [
            'rel' => 'preload',
            'href' => '',
            'crossorigin' => 'anonymous',
        ];

        foreach ($preload_assets as $asset) {
            $links[] = array_merge($defaults, $asset);
        }

        return $links;
    }

    /**
     * Output JavaScript detection script.
     *
     * Adds a `js` class to the root `<html>` element when JavaScript is detected.
     * Needs to be added in the header to avoid FOUC.
     */
    public static function javascriptDetection(): void
    {
        echo '<script>(function(html){html.className = '.
        "html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
    }
}
