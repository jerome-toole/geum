<?php

namespace Geum\Router;

use Geum\Router;

class Slot
{
    public static function init(): void
    {
        \add_action('acf/init', [static::class, 'registerBlocks']);
        \add_filter('allowed_block_types_all', [static::class, 'restrictToRouterPages'], 10, 2);
        \add_filter('block_categories_all', [static::class, 'registerBlockCategory'], 10, 2);
    }

    public static function registerBlockCategory(array $categories, $context): array
    {
        // Add Theme category if not exists
        $hasTheme = false;
        foreach ($categories as $category) {
            if ($category['slug'] === 'theme') {
                $hasTheme = true;
                break;
            }
        }

        if (! $hasTheme) {
            array_unshift($categories, [
                'slug' => 'theme',
                'title' => \__('Theme', 'geum'),
                'icon' => null,
            ]);
        }

        return $categories;
    }

    public static function registerBlocks(): void
    {
        if (! function_exists('acf_register_block_type')) {
            return;
        }

        // Template Content block
        \acf_register_block_type([
            'name' => 'template-content',
            'title' => \__('Template Content', 'geum'),
            'description' => \__('Displays dynamic page content.', 'geum'),
            'category' => 'theme',
            'icon' => 'layout',
            'mode' => 'preview',
            'supports' => [
                'mode' => false,
                'multiple' => false,
                'jsx' => false,
            ],
            'render_callback' => [static::class, 'renderTemplateContent'],
        ]);

        // Listing block
        \acf_register_block_type([
            'name' => 'listing',
            'title' => \__('Listing', 'geum'),
            'description' => \__('Displays the post listing.', 'geum'),
            'category' => 'theme',
            'icon' => 'list-view',
            'mode' => 'preview',
            'supports' => [
                'mode' => false,
                'multiple' => false,
                'jsx' => false,
            ],
            'render_callback' => [static::class, 'renderListing'],
        ]);
    }

    public static function renderTemplateContent(array $block): void
    {
        if (\is_admin() || static::isRestRequest()) {
            echo static::placeholder(\__('Template Content', 'geum'));

            return;
        }

        echo Router::renderSlot('template-content');
    }

    public static function renderListing(array $block): void
    {
        if (\is_admin() || static::isRestRequest()) {
            echo static::placeholder(\__('Listing', 'geum'));

            return;
        }

        echo Router::renderSlot('listing');
    }

    protected static function isRestRequest(): bool
    {
        if (function_exists('wp_is_rest_request')) {
            return \wp_is_rest_request();
        }

        return defined('REST_REQUEST') && REST_REQUEST;
    }

    protected static function placeholder(string $label): string
    {
        return sprintf(
            '<div class="router-slot-placeholder">
                <strong>%s</strong>
                <span>%s</span>
            </div>',
            \esc_html($label),
            \esc_html__('Dynamic content rendered on frontend.', 'geum')
        );
    }

    public static function restrictToRouterPages($allowed, $context): mixed
    {
        $post = $context->post ?? null;

        if (! $post) {
            return $allowed;
        }

        $routerBlocks = ['acf/template-content', 'acf/listing'];

        // If not a router page, remove router blocks
        if (! RouterPage::isRouterPage($post->ID)) {
            if (is_array($allowed)) {
                return array_values(array_diff($allowed, $routerBlocks));
            }

            // If $allowed is true (all blocks), we need to return all except router blocks
            // This requires getting all block types and excluding router blocks
            if ($allowed === true) {
                return true; // Can't filter when all are allowed without listing them
            }
        }

        return $allowed;
    }
}
