<?php

namespace Theme\Modules\Blog;

class CategoryTaxonomy
{
    protected const SLUG = 'category';

    public static function init(): void
    {
        \add_filter('geum/templates/taxonomies', [__CLASS__, 'filterGeumTemplatesTaxonomies']);
    }

    public static function filterGeumTemplatesTaxonomies($taxonomies): array
    {
        $taxonomies[] = self::SLUG;

        return $taxonomies;
    }
}
