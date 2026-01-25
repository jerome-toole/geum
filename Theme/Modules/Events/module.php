<?php

namespace Theme\Modules\Events;

class Module
{
    public static function init(): void
    {
        PostType::init();
        LocationTaxonomy::init();

        \add_filter('acf/settings/load_json', [__CLASS__, 'loadACFJson']);
    }

    public static function loadACFJson(array $paths): array
    {
        $paths[] = __DIR__.'/acf-json';

        return $paths;
    }
}
