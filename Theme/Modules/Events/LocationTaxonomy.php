<?php

namespace Theme\Modules\Events;

class LocationTaxonomy
{
    protected const SLUG = 'location';

    public static function init(): void
    {
        \add_action('init', [__CLASS__, 'register']);
        \add_filter('geum/templates/taxonomies', [__CLASS__, 'filterGeumTemplatesTaxonomies']);
    }

    public static function register(): void
    {
        if (! function_exists('register_extended_taxonomy')) {
            return;
        }

        \register_extended_taxonomy(
            self::SLUG,
            [
                'event',
            ],
            [
                'hierarchical' => true,
                'show_admin_column' => true,
                'show_in_rest' => true,
                'meta_box' => 'simple',
                'exclusive' => true,
                'required' => true,
                'dashboard_glance' => true,
            ],
            [
                'singular' => __('Location', 'geum'),
                'plural' => __('Locations', 'geum'),
                'slug' => self::SLUG,
            ]
        );
    }

    public static function filterGeumTemplatesTaxonomies($taxonomies): array
    {
        $taxonomies[] = self::SLUG;

        return $taxonomies;
    }
}
