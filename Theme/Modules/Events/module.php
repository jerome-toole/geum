<?php

namespace Theme\Modules\Events;

use Geum\Components\Cards;
use Geum\Components\NoContent;
use Geum\Components\Pagination;
use Geum\Components\TaxonomyFilters;
use Geum\Router;
use Geum\WordPress\PageObject;

class Module
{
    public static function init(): void
    {
        PostType::init();
        LocationTaxonomy::init();

        \add_filter('acf/settings/load_json', [__CLASS__, 'loadACFJson']);

        Router::decoratePostType('event', static::class)
            ->withPage('events')
            ->withSlot('template-content', [static::class, 'renderArchive']);
    }

    public static function renderArchive(): string
    {
        $object = PageObject::get();

        $items = [];
        while (\have_posts()) {
            \the_post();
            $items[]['object'] = \get_post();
        }

        \ob_start();

        if (! empty($items)) {
            echo TaxonomyFilters::make(object: $object);
            echo Cards::make(items: $items);
            echo Pagination::make();
        } else {
            echo NoContent::make(object: $object);
        }

        return \ob_get_clean();
    }

    public static function loadACFJson(array $paths): array
    {
        $paths[] = __DIR__.'/acf-json';

        return $paths;
    }
}
