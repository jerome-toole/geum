<?php

namespace Geum\WordPress;

use Geum\Config;

class Enqueue
{
    public static function init(): void
    {
        \add_action('wp_enqueue_scripts', [__CLASS__, 'enqueueCommentAssets']);
        \add_action('admin_enqueue_scripts', [__CLASS__, 'enqueueAdminAssets']);
        \add_action('enqueue_block_editor_assets', [__CLASS__, 'enqueueEditorAssets']);

        \add_action('init', [__CLASS__, 'dequeueWPGlobalStyles']);
        \add_action('wp_enqueue_scripts', [__CLASS__, 'dequeueWPBlockLibraryStyles']);

        \remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
        \remove_action('wp_footer', 'wp_enqueue_global_styles', 1);

        \add_action('wp_default_scripts', [__CLASS__, 'movejQueryToFooter']);
        \add_filter('wp_default_scripts', [__CLASS__, 'removejQueryMigrate']);

        \add_filter('style_loader_src', [__CLASS__, 'removeAssetVersion'], 10, 2);
        \add_filter('script_loader_src', [__CLASS__, 'removeAssetVersion'], 10, 2);

        \add_filter('geum/scripts/dependencies', [__CLASS__, 'addjQueryDependency']);
        \add_filter('geum/scripts/localization', [__CLASS__, 'addAjaxLocalization']);

        // Output Vite assets in head
        \add_action('wp_head', [__CLASS__, 'outputViteAssets'], 1);
    }

    /**
     * Output Vite assets (CSS and JS) in the head.
     */
    public static function outputViteAssets(): void
    {
        // Main frontend assets
        echo \Geum\Vite::assets([
            'assets/main.pcss',
            'assets/main.js',
        ]);
    }

    /**
     * Enqueue framework block editor assets.
     */
    public static function enqueueEditorAssets(): void
    {
        // Output Vite assets for block editor
        echo \Geum\Vite::assets([
            'assets/editor-styles.pcss',
            'assets/editor-scripts.js',
        ]);
    }

    /**
     * Enqueue framework admin assets.
     */
    public static function enqueueAdminAssets(): void
    {
        // Output Vite assets for admin
        echo \Geum\Vite::assets([
            'assets/admin-scripts.js',
        ]);
    }

    /**
     * Remove file version query argument from all enqueued styles and scripts.
     *
     * @param  string  $src  The source URL of the enqueued asset.
     * @return string The filtered URL of the enqueued asset.
     */
    public static function removeAssetVersion(string $src): string
    {
        if (strpos($src, '?ver=')) {
            $src = \remove_query_arg('ver', $src);
        }

        return $src;
    }

    /**
     * Conditionally enqueue WP comment-reply JS.
     */
    public static function enqueueCommentAssets(): void
    {
        if (\Geum\WordPress\Comments::enqueueReplyScript()) {
            \wp_enqueue_script('comment-reply');
        }
    }

    /**
     * Conditionally dequeue WP's core global styling inline css.
     *
     * Dequeues: global-styles-inline-css
     */
    public static function dequeueWPGlobalStyles(): void
    {
        if (Config::get('remove_wp_global_styles', false)) {
            \remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
            \remove_action('wp_footer', 'wp_enqueue_global_styles', 1);
        }
    }

    /**
     * Conditionally dequeue WP's block library stylesheet.
     *
     * Dequeues: /wp-includes/css/dist/block-library/style.min.css
     */
    public static function dequeueWPBlockLibraryStyles(): void
    {
        if (Config::get('remove_wp_block_library_styles', false)) {
            wp_dequeue_style('wp-block-library');
            wp_dequeue_style('wp-block-library-theme');
        }
    }

    /**
     * Removes the jQuery Migrate script bundled in WordPress core.
     */
    public static function removejQueryMigrate(&$scripts): void
    {
        if (\is_admin()) {
            return;
        }

        if (Config::get('remove_jquery_migrate', false)) {
            $scripts->remove('jquery');
            $scripts->add('jquery', false, ['jquery-core'], '1.12.4');
        }
    }

    /**
     * Moves jQuery to the footer unless it's required in the header.
     *
     * Places jQuery <script> in the footer by default. However, if a plugin requires it in
     * the header, it will automatically be moved there.
     *
     * @link https://wordpress.stackexchange.com/questions/173601/enqueue-core-jquery-in-the-footer/240612#240612
     */
    public static function movejQueryToFooter($wp_scripts): void
    {
        if (\is_admin()) {
            return;
        }

        if (Config::get('jquery_in_footer', false)) {
            $wp_scripts->add_data('jquery', 'group', 1);
            $wp_scripts->add_data('jquery-core', 'group', 1);
        }
    }

    /**
     * Adds AJAX object properties to geum-scripts via localization if required via config.
     *
     * @link https://developer.wordpress.org/reference/functions/wp_localize_script/
     *
     * @param  array  $localizations  An array of 'localizations' for geum-scripts.
     * @return array The filtered array of localizations for geum-scripts, with AJAX values conditionally added.
     */
    public static function addAjaxLocalization($localizations): array
    {
        if (Config::get('ajax_required', false)) {
            $localizations['ajax_url'] = \admin_url('admin-ajax.php');
            $localizations['home_url'] = \home_url();
        }

        return $localizations;
    }

    /**
     * Adds jQuery as a dependancy to geum-scripts if required via config.
     *
     * @param  array  $dependencies  An array of dependencies for geum-scripts.
     * @return array The filtered array of dependencies for geum-scripts, with jQuery conditionally added.
     */
    public static function addjQueryDependency($dependencies): array
    {
        if (Config::get('jquery_required', false)) {
            $dependencies[] = 'jquery';
        }

        return $dependencies;
    }
}
