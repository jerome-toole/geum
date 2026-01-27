<?php

namespace Geum\WordPress;

class Gutenberg
{
    public static function init(): void
    {
        \add_action('after_setup_theme', [__CLASS__, 'gutenbergSupport']);
        \add_filter('block_categories_all', [__CLASS__, 'gutenbergBlockCategory']);
        \add_filter('allowed_block_types_all', [__CLASS__, 'allowedBlockTypes'], 10, 2);
    }

    public static function gutenbergSupport(): void
    {
        // Add custom CSS support for Gutenberg.
        // Not to be confused with custom CSS support for TinyMCE (editor-style).
        \add_theme_support('editor-styles');

        // Add the CSS file path to be enqueued by WordPress.
        // The path to the asset must be relative to the theme root.
        $file = \Geum\Asset::extract('editor-styles.css');
        if (! empty($file)) {
            \add_editor_style(\Geum\Paths::assetPath($file, true));
        }

        // Add support for embeds to responsively keep their aspect ratio.
        \add_theme_support('responsive-embeds');

        // Deactivate the block directory.
        \remove_action('enqueue_block_editor_assets', 'wp_enqueue_editor_block_directory_assets');
        \remove_action('enqueue_block_editor_assets', 'gutenberg_enqueue_block_editor_assets_block_directory');

        // Deactivate block patterns.
        \remove_theme_support('core-block-patterns');
    }

    /**
     * Filters the Gutenberg block categories array to add a custom category.
     *
     * @link https://developer.wordpress.org/reference/hooks/block_categories/
     *
     * @param  array[]  $categories  A list of registered block categories.
     * @return array[] The filtered list of registered block categories.
     */
    public static function gutenbergBlockCategory($categories): array
    {
        // Plugin’s block category title and slug.
        $blockCategory = [
            'title' => \esc_html__('Theme Blocks', 'geum'),
            'slug' => 'theme-blocks',
        ];

        $categorySlugs = \wp_list_pluck($categories, 'slug');

        // Bail early - this category slug is already registered.
        if (in_array($blockCategory['slug'], $categorySlugs, true)) {
            return $categories;
        }

        array_unshift($categories, $blockCategory);

        return $categories;
    }

    /**
     * @param  bool|string[]  $allowedBlocks
     * @param  \WP_Block_Editor_Context  $context
     */
    public static function allowedBlockTypes($allowedBlocks, $context): array
    {
        $allowed = \apply_filters('geum/editor/allowed_blocks', []);

        if (function_exists('acf_get_block_types')) {
            $allowed = array_merge($allowed, array_keys(\acf_get_block_types()));
        }

        return $allowed;
    }
}
