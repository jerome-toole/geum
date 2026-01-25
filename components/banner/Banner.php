<?php

namespace Geum\Components;

use Geum\Component;
use Geum\ComponentBase;

/**
 * Banner Component
 *
 * Usage:
 *   use Geum\Components\Banner;
 *
 *   echo Banner::make();
 */
class Banner extends ComponentBase
{
    protected static string $name = 'banner';

    /**
     * Create a new Banner component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        array $args = [],
    ): ?static {
        return static::createFromArgs($args);
    }

    protected static function transform(array $args): array
    {
        if (! empty($args['image'])) {
            $args['image']['size'] = 'medium';
            $args['image']['sizes'] = '';
        }

        if (! empty($args['image_height'])) {
            $args['attributes']['style']['--banner--image--height'] = $args['image_height'].'px';
        }

        return $args;
    }
}
