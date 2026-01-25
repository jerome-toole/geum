<?php

namespace Geum\WordPress;

class EditHomepage
{
    /**
     * Adds an edit homepage link to the WP admin menu.
     */
    public static function init(): void
    {
        /**
         * Add the homepage edit link via submenu filter.
         *
         * @see /Geum/WordPress/Admin.php
         */
        \add_filter('geum/wordpress/admin/submenu', [__CLASS__, 'addHomepageEditLink']);
    }

    /**
     * Filters the global $submenu to add a homepage edit link to the WP admin bar.
     *
     * @param  array  $submenu  An array of WP admin menu items.
     */
    public static function addHomepageEditLink($submenu): array
    {
        // Bail early - no 'static' homepage.
        if (\get_option('show_on_front') !== 'page') {
            return $submenu;
        }

        $homepageID = \get_option('page_on_front', 0);

        // Bail early - homepage not set.
        if (empty($homepageID)) {
            return $submenu;
        }

        // Get page edit URL.
        $homepageEditLink = \get_edit_post_link($homepageID);

        // Bail early - no edit link found.
        if (empty($homepageEditLink)) {
            return $submenu;
        }

        // Create edit link array.
        $editHomepageMenuArray = [
            \__('Edit Homepage', 'geum'),
            'edit_pages',
            $homepageEditLink,
        ];

        // Add edit link.
        $submenu['edit.php?post_type=page'][] = $editHomepageMenuArray;

        return $submenu;
    }
}
