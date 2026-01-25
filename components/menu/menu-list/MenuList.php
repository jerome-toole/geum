<?php

namespace Geum\Components\Menu;

use Geum\Component;
use Geum\ComponentBase;

/**
 * MenuList Component
 *
 * Usage:
 *   use Geum\Components\Menu\MenuList\MenuList;
 *
 *   echo MenuList::make();
 */
class MenuList extends ComponentBase
{
    protected static string $name = 'menu/menu-list';

    protected static function getDefaults(): array
    {
        return [
            'depth' => 0,
        ];
    }

    /**
     * Create a new MenuList component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        array $classes = [],
        array $items = [],
        ?int $depth = null,
        mixed $max_depth = null,
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
        return ! empty($args['items']);
    }

    /**
     * Transform args before rendering.
     */
    protected static function transform(array $args): array
    {
        $args['classes'] ??= [];

        if (! empty($args['id'])) {
            $args['attributes']['id'] = $args['id'];
        }

        $args['classes'][] = 'menu-list--depth-'.$args['depth'];

        return $args;
    }
}
