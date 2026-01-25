<?php

namespace Theme\Modules\Yoast;

class Module
{
    public static function init(): void
    {
        \add_theme_support('yoast-seo-breadcrumbs');
        \add_filter('wpseo_metabox_prio', [__CLASS__, 'priority']);
        \add_filter('wpseo_breadcrumb_separator', [__CLASS__, 'breadcrumbSeparator']);
        \add_filter('wpseo_breadcrumb_output_class', [__CLASS__, 'breadcrumbWrapperClass']);
    }

    public static function priority(): string
    {
        return 'low';
    }

    public static function breadcrumbSeparator(string $markup): string
    {
        return '<span class="breadcrumbs__yoast-separator"></span>';
    }

    public static function breadcrumbWrapperClass(string $class): string
    {
        return 'breadcrumbs__yoast-wrapper';
    }
}
