<?php

namespace Theme\Modules\Pages;

class Module
{
    public static function init(): void
    {
        PostType::init();

        \add_filter('acf/settings/load_json', [__CLASS__, 'loadACFJson']);
    }

    public static function loadACFJson(array $paths): array
    {
        $paths[] = __DIR__.'/acf-json';

        return $paths;
    }
}
