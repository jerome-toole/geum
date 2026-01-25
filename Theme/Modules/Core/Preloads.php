<?php

namespace Theme\Modules\Core;

class Preloads
{
    public static function init()
    {
        \add_filter('geum/wordpress/head/preload_assets', [__CLASS__, 'addPreloads']);
    }

    public static function addPreloads(array $preloads): array
    {
        $preloads = array_merge($preloads, [
            // [
            //     'href' => \Geum\Asset::URL('static/WebFont-Regular.woff2'),
            //     'fetchpriority' => 'low',
            //     'type' => 'font/woff2',
            //     'as' => 'font',
            // ],
        ]);

        return $preloads;
    }
}
