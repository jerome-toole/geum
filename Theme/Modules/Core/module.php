<?php

namespace Theme\Modules\Core;

class Module
{
    public static function init(): void
    {
        Admin::init();
        Excerpt::init();
        Menus::init();
        MimeTypes::init();
        Preloads::init();
        Sidebars::init();
    }
}
