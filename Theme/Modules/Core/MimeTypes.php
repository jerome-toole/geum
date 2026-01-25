<?php

namespace Theme\Modules\Core;

class MimeTypes
{
    public static function init()
    {
        \add_filter('geum/wordpress/upload_mimes/types', [__CLASS__, 'addThemeMimeTypes']);
    }

    public static function addThemeMimeTypes(array $types): array
    {
        $types = array_merge($types, [
            // 'svg' => 'image/svg+xml',
            // 'msg'  => 'application/vnd.ms-outlook',
            // 'flv'  => 'video/x-flv',
            // 'xls'  => 'application/application/excel',
            // 'xlsx' => 'application/application/vnd.ms-excel',
            // 'tiff' => 'image/tiff',
            // 'tif'  => 'image/tiff',
            // 'psd'  => 'image/photoshop',
            // 'xlsx' => 'application/application/vnd.ms-excel',
            // 'swf'  => 'application/x-shockwave-flash',
        ]);

        return $types;
    }
}
