<?php

namespace Geum\Router;

use Geum\Router;

class RouterPage
{
    protected static array $pages = [];

    public static function init(): void
    {
        \add_action('init', [static::class, 'ensureAllPages'], 20);
        \add_action('before_delete_post', [static::class, 'preventDeletion']);
        \add_filter('wp_insert_post_data', [static::class, 'preventStructuralChanges'], 10, 2);
        \add_filter('page_link', [static::class, 'filterPageLink'], 10, 2);
        \add_filter('display_post_states', [static::class, 'addRoleBadge'], 10, 2);
        \add_filter('page_row_actions', [static::class, 'removeTrashAction'], 10, 2);
        \add_filter('bulk_actions-edit-page', [static::class, 'removeBulkTrash']);
    }

    public static function ensure(string $role, array $attributes = []): void
    {
        static::$pages[$role] = $attributes;
    }

    public static function ensureAllPages(): void
    {
        foreach (static::$pages as $role => $attributes) {
            $pageId = \get_option("geum_router_page_{$role}");
            $page = $pageId ? \get_post($pageId) : null;

            if (! $page || $page->post_status === 'trash') {
                static::create($role, $attributes);
            }
        }
    }

    public static function create(string $role, array $attributes = []): int
    {
        $title = $attributes['title'] ?? ucwords(str_replace('-', ' ', $role));
        $content = $attributes['content'] ?? '';

        $pageId = \wp_insert_post([
            'post_type' => 'page',
            'post_title' => $title,
            'post_name' => $role,
            'post_status' => 'publish',
            'post_content' => $content,
            'meta_input' => [
                '_is_router_page' => true,
                '_router_role' => $role,
            ],
        ]);

        if ($pageId && ! \is_wp_error($pageId)) {
            \update_option("geum_router_page_{$role}", $pageId, false);
        }

        return $pageId;
    }

    public static function isRouterPage(int $postId): bool
    {
        return (bool) \get_post_meta($postId, '_is_router_page', true);
    }

    public static function getRole(int $postId): ?string
    {
        $role = \get_post_meta($postId, '_router_role', true);

        return $role ?: null;
    }

    public static function getPageByRole(string $role): ?\WP_Post
    {
        $pageId = \get_option("geum_router_page_{$role}");

        if (! $pageId) {
            return null;
        }

        // Filter for multi-lingual support (e.g., Polylang).
        $pageId = \apply_filters('geum/router/page-id', $pageId, $role);

        $page = \get_post($pageId);

        return $page instanceof \WP_Post ? $page : null;
    }

    public static function preventDeletion(int $postId): void
    {
        if (static::isRouterPage($postId)) {
            \wp_die(
                \__('Router pages cannot be deleted.', 'geum'),
                \__('Action Blocked', 'geum'),
                ['back_link' => true]
            );
        }
    }

    public static function preventStructuralChanges(array $data, array $postarr): array
    {
        if (empty($postarr['ID']) || ! static::isRouterPage($postarr['ID'])) {
            return $data;
        }

        $original = \get_post($postarr['ID']);

        if (! $original) {
            return $data;
        }

        // Preserve slug and parent
        $data['post_name'] = $original->post_name;
        $data['post_parent'] = $original->post_parent;

        // Prevent status change to trash
        if ($data['post_status'] === 'trash') {
            $data['post_status'] = $original->post_status;
        }

        return $data;
    }

    public static function filterPageLink(string $link, int $postId): string
    {
        static $resolving = [];

        if (isset($resolving[$postId])) {
            return $link;
        }

        if (! static::isRouterPage($postId)) {
            return $link;
        }

        $role = static::getRole($postId);
        if (! $role) {
            return $link;
        }

        $route = Router::getRouteByRole($role);
        if ($route) {
            $resolving[$postId] = true;
            $url = $route->getUrl();
            unset($resolving[$postId]);

            return $url;
        }

        return $link;
    }

    public static function addRoleBadge(array $states, \WP_Post $post): array
    {
        if (static::isRouterPage($post->ID)) {
            $role = static::getRole($post->ID);
            $label = ucwords(str_replace('-', ' ', $role ?? ''));
            $states['router_page'] = sprintf(
                /* translators: %s: Router page role name */
                \__('Role: %s', 'geum'),
                $label
            );
        }

        return $states;
    }

    public static function removeTrashAction(array $actions, \WP_Post $post): array
    {
        if (static::isRouterPage($post->ID)) {
            unset($actions['trash']);
        }

        return $actions;
    }

    public static function removeBulkTrash(array $actions): array
    {
        // Only remove trash for router pages
        // This is a simplified approach; a more sophisticated version
        // would check which pages are selected
        return $actions;
    }
}
