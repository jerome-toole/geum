<?php

namespace Theme\Modules\Articles;

class TagTaxonomy
{
    protected const SLUG = 'post_tag';

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
