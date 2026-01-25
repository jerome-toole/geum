<?php

namespace Theme\Controllers;

class SearchController
{
    /**
     * Prepare the search query.
     */
    public function prepare(): void
    {
        // Modify query if needed
    }

    /**
     * Render the search results.
     */
    public static function renderResults(): string
    {
        \ob_start();

        if (\have_posts()) {
            echo '<div class="search-results">';
            while (\have_posts()) {
                \the_post();
                \get_template_part('template-parts/card');
            }
            echo '</div>';

            \the_posts_pagination([
                'prev_text' => \__('Previous', 'theme'),
                'next_text' => \__('Next', 'theme'),
            ]);
        } else {
            echo '<p>'.\__('No results found.', 'theme').'</p>';
        }

        return \ob_get_clean();
    }
}
