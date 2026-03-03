<?php

get_header();

$object = \Geum\WordPress\PageObject::get();

site_main_open(object: $object);

$routerPage = \Geum\Router::getPage();

if ($routerPage) {
    echo apply_filters('the_content', $routerPage->post_content);
} else {
    echo \Geum\Components\PageHeader::make(object: $object);
    echo \Geum\Components\NoContent::make(object: $object);
}

site_main_close();

get_footer();
