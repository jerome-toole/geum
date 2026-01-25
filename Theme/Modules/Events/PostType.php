<?php

namespace Theme\Modules\Events;

class PostType
{
    protected const SLUG = 'event';

    public static function init(): void
    {
        \add_action('init', [__CLASS__, 'register']);
        \add_action('acf/init', [__CLASS__, 'addSettingsPage']);
        \add_filter('geum/templates/post-types', [__CLASS__, 'filterGeumTemplatesPostTypes']);
    }

    public static function register(): void
    {
        if (! function_exists('register_extended_post_type')) {
            return;
        }

        \register_extended_post_type(self::SLUG, [
            'public' => true,
            'has_archive' => true,
            'hierarchical' => false,
            'show_in_rest' => true,
            'menu_position' => 25,
            'menu_icon' => 'dashicons-calendar',
            'enter_title_here' => 'Event Name',
            'supports' => [
                'title',
                'editor',
                'excerpt',
                'revisions',
                'thumbnail',
                'author',
                'custom-fields',
            ],
            'taxonomies' => [
                'location',
            ],
            'template' => [
                [
                    'core/paragraph',
                    [
                        'placeholder' => 'Add content...',
                    ],
                ],
            ],
            'admin_filters' => [
                'location' => [
                    'taxonomy' => 'location',
                ],
            ],
            'admin_cols' => [
                'thumbnail' => [
                    'title' => 'Thumbnail',
                    'featured_image' => 'thumbnail',
                    'width' => 80,
                    'height' => 80,
                ],
                'title' => [
                    'title' => 'Title',
                ],
                'author' => [
                    'title' => 'Author',
                ],
                'location' => [
                    'taxonomy' => 'location',
                ],
                'updated' => [
                    'title' => 'Updated',
                    'post_field' => 'post_modified',
                    'date_format' => 'Y/m/d \a\t H:i a',
                ],
            ],
        ], [
            'singular' => __('Event', 'geum'),
            'plural' => __('Events', 'geum'),
            'slug' => self::SLUG,
        ]);
    }

    public static function addSettingsPage(): void
    {
        if (! function_exists('acf_add_options_sub_page')) {
            return;
        }

        \acf_add_options_sub_page([
            'page_title' => __('Events Settings', 'geum'),
            'menu_title' => __('Events Settings', 'geum'),
            'menu_slug' => 'acf-options-events-settings',
            'parent_slug' => 'edit.php?post_type='.self::SLUG,
        ]);
    }

    public static function filterGeumTemplatesPostTypes($postTypes)
    {
        $postTypes[] = self::SLUG;

        return $postTypes;
    }
}
