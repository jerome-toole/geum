<?php

namespace Geum\Components\Menu;

use Geum\Component;
use Geum\ComponentBase;

/**
 * MenuItem Component
 *
 * Usage:
 *   use Geum\Components\Menu\MenuItem\MenuItem;
 *
 *   echo MenuItem::make();
 */
class MenuItem extends ComponentBase
{
    protected static string $name = 'menu/menu-item';

    /**
     * Create a new MenuItem component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        array $classes = [],
        mixed $item = null,
        int $depth = 0,
        mixed $max_depth = null,
        bool $display_submenu = false,
        ...$others
    ): ?static {
        $args = static::mergeArgs(get_defined_vars());

        $args = static::processArgs($args);

        if ($args === null) {
            return null;
        }

        return new static($args);
    }

    /**
     * Validate args before rendering.
     */
    protected static function validate(array $args): bool
    {
        return ! empty($args['item']) && $args['item'] instanceof \WP_Post;
    }

    /**
     * Transform args before rendering.
     */
    protected static function transform(array $args): array
    {
        $item = $args['item'];

        $args['classes'] = array_merge($args['classes'] ?? [], $item->classes);

        $args['attributes']['id'] = 'menu-item-'.$item->ID;

        $args['link'] = [
            'url' => $item->url,
            'content' => $item->title,
            'target' => $item->target ?: '',
            'attributes' => [
                'title' => $item->attr_title ?: null,
            ],
        ];

        if (! empty($item->xfn)) {
            $args['link']['attributes']['rel'][] = $item->xfn;
        }

        if (! empty($item->children) && (empty($args['max_depth']) || $args['max_depth'] > $args['depth'] + 1)) {
            $args['display_submenu'] = true;

            $args['sub-menu-attributes'] = [
                'id' => 'sub-menu-'.$item->ID,
                'class' => \Geum\Helpers::buildClasses([
                    'sub-menu',
                    'sub-menu--depth-'.$args['depth'],
                ]),

                // Initially hide sub-menus.
                'hidden' => true,
                'aria-hidden' => 'true',
            ];

            $args['button'] = [
                'content' => \__('Expand or collapse a sub menu', 'geum'),
                'screen_reader_text' => true,
                'classes' => ['sub-menu-toggler'],
                'attributes' => [
                    'aria-expanded' => 'false',
                    'aria-controls' => 'sub-menu-'.$item->ID,
                    'id' => 'sub-menu-'.$item->ID.'-toggler',
                ],
            ];
        }

        // Add multiple
        $args['classes'] = static::buildMenuItemClasses($args['classes'], $item, $args);

        return $args;
    }

    /**
     * Generate an array of menu item classes based on the item and its component's arguments.
     *
     * @link https://developer.wordpress.org/reference/functions/wp_nav_menu/#menu-item-css-classes
     */
    protected static function buildMenuItemClasses(array $classes, \WP_Post $item, array $args): array
    {
        // Depth class.
        $classes[] = 'menu-item--depth-'.$args['depth'];

        // Homepage class.
        if ($item->type === 'post_type' && (int) \get_option('page_on_front') === (int) $item->object_id) {
            $classes[] = 'menu-item--home';
        }

        // Current Menu Item class.
        if (! empty($item->is_current_item)) {
            $classes[] = 'menu-item--current';
        }

        // Parent Menu Item class.
        if (! empty($item->is_current_parent)) {
            $classes[] = 'current-menu-parent';
        }

        // Ancestor Menu Item class.
        if (! empty($item->is_current_ancestor)) {
            $classes[] = 'current-menu-ancestor';
        }

        // Menu Items with children (i.e. a submenu).
        if (! empty($item->children) && (empty($args['max_depth']) || $args['max_depth'] > $args['depth'] + 1)) {
            $classes[] = 'menu-item--has-children';
        }

        return $classes;
    }
}
