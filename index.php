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
    echo \Geum\Components\TemplateLoop::make();
}

site_main_close();

get_footer();
