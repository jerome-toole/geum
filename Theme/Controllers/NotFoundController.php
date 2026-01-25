<?php

namespace Theme\Controllers;

class NotFoundController
{
    /**
     * Render the 404 content.
     */
    public static function renderContent(): string
    {
        \ob_start();
        ?>
        <div class="not-found-content">
            <p><?php \esc_html_e('The page you are looking for could not be found.', 'theme'); ?></p>
        </div>
        <?php
        return \ob_get_clean();
    }
}
