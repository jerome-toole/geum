<?php

get_header();

$object = \Geum\WordPress\PageObject::get();

site_main_open(object: $object);

$routerPage = \Geum\Router::getPage();

if ($routerPage) {
    if (! has_block('acf/page-header', $routerPage->ID)) {
        echo \Geum\Components\PageHeader::make(object: $object);
    }

    echo apply_filters('the_content', $routerPage->post_content);
} else {
    $items = [];
    while (have_posts()) {
        the_post();
        $items[]['object'] = get_post();
    }

    if (! has_block('acf/page-header')) {
        echo \Geum\Components\PageHeader::make(object: $object);
    }

    if (! empty($items)) {
        echo \Geum\Components\TaxonomyFilters::make(
            object: $object,
        );

        echo \Geum\Components\Cards::make(items: $items);
        echo \Geum\Components\Pagination::make();
    } else {
        echo \Geum\Components\NoContent::make(
            object: $object,
        );
    }
}

site_main_close();

get_footer();
