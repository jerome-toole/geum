<?php

namespace Geum\WordPress;

use Geum\Router;

class Admin
{
    public static function init(): void
    {
        // Set environment type immediately (needed before modules load)
        self::setEnvironmentType();

        \add_action('init', [__CLASS__, 'disallowFileEdit']);
        \add_action('admin_head', [__CLASS__, 'addWPAdminSubmenuGlobalFilter'], 15);
        \add_action('wp_dashboard_setup', [__CLASS__, 'removeDraftWidget'], 1);
        \add_filter('get_user_option_admin_color', [__CLASS__, 'adminColor']);

        \add_action('admin_menu', [__CLASS__, 'addMenusTopLevelItem']);
        \add_filter('custom_menu_order', '__return_true');
        \add_filter('menu_order', [__CLASS__, 'reorderAdminMenu']);

        // Post archive page link to all posts admin screen.
        \add_action('admin_bar_menu', [__CLASS__, 'addViewAllPostsToArchivePages'], 80);

        // Router page edit link for decorated routes.
        \add_action('admin_bar_menu', [__CLASS__, 'addEditRouterPageLink'], 81);
    }

    /**
     * Add a top-level "Menus" item linking directly to nav-menus.php.
     */
    public static function addMenusTopLevelItem(): void
    {
        \add_menu_page('Menus', 'Menus', 'manage_options', 'nav-menus.php', '', 'dashicons-menu-alt3');
    }

    /**
     * Reorder the WP admin menu into three zones:
     *
     *  Zone 1 — Content post types (dynamic, ordered by menu_position)
     *  Zone 2 — Content utilities: Forms, Media, Menus
     *  Zone 3 — Admin/settings + any unrecognised plugin items
     */
    public static function reorderAdminMenu(array $menu_order): array
    {
        $separators = ['separator1', 'separator2', 'separator-last'];

        // Zone 1: all public post types ordered by menu_position (excludes admin-only types like ACF).
        $post_type_zone = self::getPostTypeSlugs();

        // Zone 2: content utilities — only include items actually registered in this install.
        $utility_zone = array_values(array_filter(
            ['gf_edit_forms', 'upload.php', 'nav-menus.php'],
            fn ($slug) => in_array($slug, $menu_order, true)
        ));

        // Zone 3: core WP admin items — only include if present.
        $admin_zone = array_values(array_filter(
            ['themes.php', 'plugins.php', 'users.php', 'tools.php', 'options-general.php'],
            fn ($slug) => in_array($slug, $menu_order, true)
        ));

        // Anything not explicitly placed (e.g. new plugins) goes at the end of zone 3.
        $placed = array_merge(['index.php'], $post_type_zone, $utility_zone, $admin_zone, $separators);
        $unknown = array_values(array_diff($menu_order, $placed));

        return array_merge(
            ['index.php', 'separator1'],
            $post_type_zone,
            ['separator2'],
            $utility_zone,
            ['separator-last'],
            $admin_zone,
            $unknown,
        );
    }

    /**
     * Return menu slugs for all public post types, ordered by menu_position.
     *
     * Using `public => true` naturally excludes admin-only post types (ACF field groups, etc.)
     * while including all content post types regardless of how many there are.
     *
     * @return string[]
     */
    protected static function getPostTypeSlugs(): array
    {
        $post_types = \get_post_types(['public' => true, 'show_ui' => true], 'objects');

        uasort($post_types, fn ($a, $b) => ($a->menu_position ?? PHP_INT_MAX) <=> ($b->menu_position ?? PHP_INT_MAX));

        $slugs = [];
        foreach ($post_types as $pt) {
            if ($pt->name === 'attachment') {
                continue;
            }
            $slugs[] = $pt->name === 'post' ? 'edit.php' : 'edit.php?post_type=' . $pt->name;
        }

        return $slugs;
    }

    /**
     * Prevent users editing plugin and theme files.
     *
     * Easier than looping through all defined user roles and reassigning relevant capabilities.
     *
     * @return void
     */
    public static function disallowFileEdit()
    {
        define('DISALLOW_FILE_EDIT', true);
    }

    /**
     * Sets the environment type from .env file if not already defined.
     */
    public static function setEnvironmentType(): void
    {
        if (defined('WP_ENVIRONMENT_TYPE')) {
            return;
        }

        $env_path = \get_theme_file_path('.env');

        if (! file_exists($env_path)) {
            return;
        }

        $env_type = self::getEnvValue($env_path, 'WP_ENVIRONMENT_TYPE');

        if ($env_type) {
            define('WP_ENVIRONMENT_TYPE', $env_type);
        }
    }

    /**
     * Parse a value from an .env file.
     */
    protected static function getEnvValue(string $path, string $key): ?string
    {
        $contents = file_get_contents($path);

        if (preg_match('/^'.preg_quote($key, '/').'=(.*)$/m', $contents, $matches)) {
            return trim($matches[1], " \t\n\r\0\x0B\"'");
        }

        return null;
    }

    /**
     * Filter the 'admin_color' user option when .env file is present (e.g. a local or development environment).
     *
     * @link https://developer.wordpress.org/reference/hooks/get_user_option_option/
     *
     * @return string The filtered admin_color option.
     */
    public static function adminColor($value)
    {
        if (\wp_get_environment_type() === 'development') {
            return 'midnight';
        }

        return $value;
    }

    /**
     * Remove 'quick edit' widget.
     */
    public static function removeDraftWidget(): void
    {
        \remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    }

    /**
     * Filters the global $submenu to allow adding custom links to the WP admin bar.
     *
     * NOTE: Adding a filter to a WP global isn't ideal. However, as there's
     * no easy way to add custom links to the (sub)menu then this approach
     * will do for now. Some enhancements to the menu API have been suggested
     * on trac (see links below), so could be good options in the future.
     *
     * @link https://core.trac.wordpress.org/ticket/12718
     * @link https://core.trac.wordpress.org/ticket/39050
     */
    public static function addWPAdminSubmenuGlobalFilter(): void
    {
        global $submenu;

        $submenu = \apply_filters('geum/wordpress/admin/submenu', $submenu);
    }

    /**
     * Add an 'Edit all {Post Type}' button to the WP admin bar when viewing a post type
     * archive page on the front-end, which is linked to the admin view all {post-type} screen.
     * Allows users to quickly get to the full admin list of posts from the archive page.
     *
     * @link https://developer.wordpress.org/reference/hooks/admin_bar_menu/
     *
     * @param  WP_Admin_Bar  $adminBar  The WP_Admin_Bar instance, passed by reference.
     */
    public static function addViewAllPostsToArchivePages($adminBar): void
    {
        if (! \current_user_can('edit_posts') || \is_admin()) {
            return;
        }

        $queried_object = \Geum\WordPress\PageObject::get();

        // Bail early - not on an template page.
        if (! \is_post_type_archive() && ! $queried_object instanceof \WP_Post_Type) {
            return;
        }

        $adminBar->add_menu([
            'id' => 'geum-all-posts',
            'title' => sprintf(
                // translators: 1: opening html tags. 2: post type name. 3: closing html tags.
                \_x('%sEdit all %s%s', 'Admin bar edit link', 'geum'),
                '<span class="ab-icon" aria-hidden="true"></span><span class="ab-label">',
                $queried_object->label,
                '</span>'
            ),
            'href' => \admin_url('edit.php?post_type='.$queried_object->name),
            'meta' => [
                'title' => sprintf(
                    // translators: post type name.
                    \_x('View all %s admin page', 'Admin bar edit link title', 'geum'),
                    $queried_object->label,
                ),
                'class' => 'geum-ab-item geum-edit-template',
            ],
        ]);
    }

    /**
     * Add an 'Edit Page' link to the WP admin bar when viewing a decorated route
     * that has an associated RouterPage. Allows editors to quickly open the linked page
     * in the block editor to manage surrounding content.
     *
     * @link https://developer.wordpress.org/reference/hooks/admin_bar_menu/
     *
     * @param  \WP_Admin_Bar  $adminBar  The WP_Admin_Bar instance, passed by reference.
     */
    public static function addEditRouterPageLink(\WP_Admin_Bar $adminBar): void
    {
        if (\is_admin() || ! \current_user_can('edit_pages')) {
            return;
        }

        $route = Router::current();
        if (! $route) {
            return;
        }

        $page = Router::getPage();
        if (! $page) {
            return;
        }

        if (! \current_user_can('edit_post', $page->ID)) {
            return;
        }

        $adminBar->add_menu([
            'id' => 'geum-edit-router-page',
            'title' => sprintf(
                '%sEdit Page%s',
                '<span class="ab-icon" aria-hidden="true"></span><span class="ab-label">',
                '</span>'
            ),
            'href' => \admin_url("post.php?post={$page->ID}&action=edit"),
            'meta' => [
                'class' => 'geum-ab-item geum-edit-router-page',
            ],
        ]);
    }
}
