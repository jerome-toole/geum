<?php

namespace Theme\Controllers;

class ArchiveController
{
    /**
     * Prepare the archive query.
     */
    public function prepare(): void
    {
        // Modify query if needed
        // \add_action('pre_get_posts', function ($query) {
        //     if ($query->is_main_query() && ! \is_admin()) {
        //         $query->set('posts_per_page', 12);
        //     }
        // });
    }

    /**
     * Render the archive loop.
     */
    public static function renderLoop(): string
    {
        \ob_start();

        if (\have_posts()) {
            echo '<div class="archive-grid">';
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
            echo '<p>'.\__('No posts found.', 'theme').'</p>';
        }

        return \ob_get_clean();
    }
}
