<?php

namespace Geum\WordPress;

use Geum\Config;

/**
 * Removes all comment related functionality and admin views.
 */
class Comments
{
    public static function init(): void
    {
        if (self::areCommentsEnabled()) {
            return;
        }

        \add_action('admin_init', [__CLASS__, 'removeAdminCommentMetabox']);
        \add_action('admin_init', [__CLASS__, 'removePTCommentSupport']);
        \add_action('current_screen', [__CLASS__, 'redirectCommentAdminPage']);

        \add_action('admin_menu', [__CLASS__, 'removeAdminMenuCommentsPage']);

        // Priority 250 is after all menus are registered.
        \add_action('admin_bar_menu', [__CLASS__, 'removeAdminBarCommentsLinks'], 250);

        // Close comments on the front-end.
        \add_filter('comments_open', '__return_false', 20, 2);
        \add_filter('pings_open', '__return_false', 20, 2);

        // Hide existing comments.
        \add_filter('comments_array', '__return_empty_array', 10, 2);
    }

    /**
     * Redirect any user trying to access comments page.
     *
     * @param  WP_Screen  $screen  Current WP_Screen object.
     */
    public static function redirectCommentAdminPage($screen): void
    {
        if (isset($screen->base) && $screen->base === 'edit-comments') {
            \wp_redirect(\admin_url());
            exit;
        }
    }

    /**
     * Remove comments metabox from dashboard.
     */
    public static function removeAdminCommentMetabox(): void
    {
        \remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
    }

    /**
     * Disable support for comments and trackbacks in post types.
     */
    public static function removePTCommentSupport(): void
    {
        foreach (\get_post_types() as $post_type) {
            if (\post_type_supports($post_type, 'comments')) {
                \remove_post_type_support($post_type, 'comments');
                \remove_post_type_support($post_type, 'trackbacks');
            }
        }
    }

    /**
     * Remove comments page in admin menu.
     */
    public static function removeAdminMenuCommentsPage(): void
    {
        \remove_menu_page('edit-comments.php');
    }

    /**
     * Remove comments links from admin bar.
     *
     * @param  WP_Admin_Bar  $wp_admin_bar  WP_Admin_Bar instance, passed by reference.
     */
    public static function removeAdminBarCommentsLinks($wp_admin_bar): void
    {
        $wp_admin_bar->remove_menu('comments');
    }

    /**
     * Determine whether the WP core comment-reply script should be enqueued.
     *
     * @return bool True if the comment-reply script should be enqueued, false otherwise.
     */
    public static function enqueueReplyScript(): bool
    {
        return self::areCommentsEnabled()
            && \is_singular()
            && \comments_open()
            && \get_option('thread_comments');
    }

    /**
     * Determine whether all comment related functionality should be enabled for WordPress.
     *
     * @return bool Whether all comment related functionality should be enabled.
     */
    public static function areCommentsEnabled(): bool
    {
        return ! Config::get('deactivate_comments', false);
    }
}
