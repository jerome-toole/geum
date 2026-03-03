<?php

namespace Theme\Modules\Articles;

class ArticlesModule
{
    public static function init(): void
    {
        PostType::init();
        CategoryTaxonomy::init();
        TagTaxonomy::init();

        \add_filter('acf/settings/load_json', [__CLASS__, 'loadACFJson']);
    }

    public static function loadACFJson(array $paths): array
    {
        $paths[] = __DIR__.'/acf-json';

        return $paths;
    }
}
