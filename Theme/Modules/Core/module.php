<?php

namespace Theme\Modules\Core;

class CoreModule
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
