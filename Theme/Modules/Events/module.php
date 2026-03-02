<?php

namespace Theme\Modules\Events;

use Geum\Components\Card;
use Geum\Router;

class Module
{
    public static function init(): void
    {
        PostType::init();
        LocationTaxonomy::init();

        \add_filter('acf/settings/load_json', [__CLASS__, 'loadACFJson']);

        Router::decoratePostType('event', static::class)
            ->withPage('events')
            ->withSlot('template-content', [static::class, 'renderArchive']);
    }

    public static function renderArchive(): string
    {
        \ob_start();

        if (\have_posts()) {
            echo '<div class="archive-grid">';
            while (\have_posts()) {
                \the_post();
                echo Card::make(object: \get_post());
            }
            echo '</div>';

            \the_posts_pagination([
                'prev_text' => \__('Previous', 'theme'),
                'next_text' => \__('Next', 'theme'),
            ]);
        } else {
            echo '<p>'.\__('No events found.', 'theme').'</p>';
        }

        return \ob_get_clean();
    }

    public static function loadACFJson(array $paths): array
    {
        $paths[] = __DIR__.'/acf-json';

        return $paths;
    }
}
