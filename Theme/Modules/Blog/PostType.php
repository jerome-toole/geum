<?php

namespace Theme\Modules\Blog;

class PostType
{
    protected const SLUG = 'post';

    public static function init(): void
    {
        \add_filter('geum/templates/post-types', [__CLASS__, 'filterGeumTemplatesPostTypes']);
    }

    public static function filterGeumTemplatesPostTypes($postTypes)
    {
        $postTypes[] = self::SLUG;

        return $postTypes;
    }
}
