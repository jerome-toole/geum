<?php

namespace Theme\Controllers;

use Geum\Components\Cards;
use Geum\Components\NoContent;
use Geum\Components\Pagination;
use Geum\WordPress\PageObject;

class ArchiveController
{
    public function prepare(): void
    {
        // Modify query if needed
        // \add_action('pre_get_posts', function ($query) {
        //     if ($query->is_main_query() && ! \is_admin()) {
        //         $query->set('posts_per_page', 12);
        //     }
        // });
    }

    public static function renderLoop(): string
    {
        $object = PageObject::get();

        $items = [];
        while (\have_posts()) {
            \the_post();
            $items[]['object'] = \get_post();
        }

        \ob_start();

        if (! empty($items)) {
            echo Cards::make(items: $items);
            echo Pagination::make();
        } else {
            echo NoContent::make(object: $object);
        }

        return \ob_get_clean();
    }
}
