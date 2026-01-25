<?php

namespace Geum\Components;

use Geum\Component;
use Geum\ComponentBase;

/**
 * Image Component
 *
 * Usage:
 *   use Geum\Components\Image;
 *
 *   echo Image::make();
 */
class Image extends ComponentBase
{
    protected static string $name = 'image';

    protected static function getDefaults(): array
    {
        return [
            'size' => 'medium_large',
        ];
    }

    /**
     * Create a new Image component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        array $classes = [],
        mixed $id = null,
        ?string $title = null,
        ?string $alt = null,
        ?string $size = null,
        array $attributes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function transform(array $args): array
    {
        if (! empty($args['ID'])) {
            $args['id'] = $args['ID'];
        }

        if (! empty($args['sizes']) && ! is_array($args['sizes'])) {
            $args['attributes']['sizes'] = $args['sizes'];
        }

        if (! empty($args['id'])) {
            $args['output'] = wp_get_attachment_image(
                $args['id'],
                $args['size'],
                false,
                $args['attributes']
            );
        }

        return $args;
    }
}
