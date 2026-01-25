<?php

namespace Geum\WordPress;

class ThemeSetup
{
    public static function init(): void
    {
        \add_action('after_setup_theme', [__CLASS__, 'setup']);
    }

    public static function setup(): void
    {
        // Make theme available for translation.
        // @link https://codex.wordpress.org/Function_Reference/load_theme_textdomain
        \load_theme_textdomain('geum', \get_template_directory().'/languages');

        // Add default posts and comments RSS feed links to head.
        // @link https://codex.wordpress.org/Automatic_Feed_Links
        //add_theme_support( 'automatic-feed-links' );

        // Let WordPress manage the document title.
        // @link http://codex.wordpress.org/Function_Reference/add_theme_support#Title_Tag
        \add_theme_support('title-tag');

        // HTML5 markup support
        // @link http://codex.wordpress.org/Function_Reference/add_theme_support#HTML5
        \add_theme_support('html5', [
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'script',
            'style',
        ]);
    }
}
