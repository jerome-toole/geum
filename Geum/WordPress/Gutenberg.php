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

        // Add editor styles from build (no HMR, always uses built CSS)
        $file = \Geum\Asset::extract('editor-styles.css');

        if (! empty($file)) {
            \add_editor_style(\Geum\Paths::assetPath('build/'.$file, true));
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
     * Filter allowed block types.
     *
     * @param  bool|string[]  $allowedBlocks  Array of allowed block types or true for all.
     * @param  \WP_Block_Editor_Context  $context  Editor context.
     * @return string[]
     */
    public static function allowedBlockTypes($allowedBlocks, $context): array
    {
        $allowed = [
            'core/paragraph',
            'core/image',
            'core/heading',
            'core/gallery',
            'core/list',
            'core/list-item',
            'core/quote',
            'core/shortcode',
            'core/button',
            'core/buttons',
            'core/columns',
            'core/column',
            // 'core/cover',
            'core/group',
            'core/embed',
            'core/freeform',
            'core/html',
            'core/missing',
            'core/separator',
            'core/block',
            'core/table',
        ];

        // Add all ACF blocks
        if (function_exists('acf_get_block_types')) {
            $allowed = array_merge($allowed, array_keys(\acf_get_block_types()));
        }

        return $allowed;
    }
}
