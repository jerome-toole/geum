<?php

namespace Theme\Modules\Core;

class Sidebars
{
    public static function init(): void
    {
        \add_action('widgets_init', [__CLASS__, 'registerSidebars']);
    }

    public static function registerSidebars(): void
    {
        $sidebars = [
            // [
            //     'name'          => esc_html__('Sidebar', 'geum'),
            //     'id'            => 'sidebar-1',
            //     'description'   => esc_html__('Add widgets here.', 'geum'),
            //     'before_widget' => '<section id="%1$s" class="widget %2$s">',
            //     'after_widget'  => '</section>',
            //     'before_title'  => '<h4 class="widget-title">',
            //     'after_title'   => '</h4>',
            // ]
        ];

        if (! empty($sidebars)) {
            foreach ($sidebars as $sidebar) {
                \register_sidebar($sidebar);
            }
        }
    }
}
