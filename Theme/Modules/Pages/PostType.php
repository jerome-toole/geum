<?php

namespace Theme\Modules\Pages;

class PostType
{
    protected const SLUG = 'page';

    public static function init(): void
    {
        \add_filter('register_post_type_args', [__CLASS__, 'filterRegisterPostTypeArgs'], 10, 2);
    }

    public static function filterRegisterPostTypeArgs($args, $post_type)
    {
        if ($post_type !== self::SLUG) {
            return $args;
        }

        $args['template'] = [
            ['acf/page-header'],
        ];

        return $args;
    }
}
