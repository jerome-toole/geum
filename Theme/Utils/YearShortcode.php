<?php

namespace Theme\Utils;

class YearShortcode
{
    public static function init()
    {
        \add_shortcode('year', [__CLASS__, 'render']);
    }

    public static function render()
    {
        return date('Y');
    }
}
