<?php

get_header();

$object = \Geum\WordPress\PageObject::get();

site_main_open(object: $object);

ob_start();
\Geum\Router::renderPage();
$routerContent = ob_get_clean();

if ($routerContent) {
    echo $routerContent;
} else {
    $items = [];
    while (have_posts()) {
        the_post();
        $items[]['object'] = get_post();
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
