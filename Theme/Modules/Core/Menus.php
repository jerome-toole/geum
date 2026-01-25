<?php

namespace Theme\Modules\Core;

class Menus
{
    public static function init()
    {
        \add_filter('after_setup_theme', [__CLASS__, 'registerThemeMenus']);
    }

    public static function registerThemeMenus(): void
    {
        \register_nav_menus([
            'header' => _x('Header', 'Menu name', 'geum'),
            'footer-1' => _x('Footer 1', 'Menu name', 'geum'),
            'footer-2' => _x('Footer 2', 'Menu name', 'geum'),
        ]);
    }
}
