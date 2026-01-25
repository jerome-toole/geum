<?php

namespace Theme\Modules\Core;

class Excerpt
{
    public static function init()
    {
        \add_filter('excerpt_more', [__CLASS__, 'filterExcerptMore']);
        \add_filter('excerpt_length', [__CLASS__, 'filterExcerptLength']);
    }

    public static function filterExcerptMore()
    {
        return '&hellip;';
    }

    public static function filterExcerptLength()
    {
        return 20;
    }
}
