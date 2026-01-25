<?php

namespace Geum\Components;

use Geum\Component;
use Geum\ComponentBase;

/**
 * Group Component
 *
 * Usage:
 *   use Geum\Components\Group;
 *
 *   echo Group::make();
 */
class Group extends ComponentBase
{
    protected static string $name = 'group';

    protected static function getDefaults(): array
    {
        return [
            'blockTemplate' => [
                ['core/heading', ['level' => 2, 'placeholder' => 'Add your heading']],
                ['core/paragraph', ['placeholder' => 'Add your paragraph']],
                ['core/button', ['placeholder' => 'Add your button']],
            ],
        ];
    }

    /**
     * Create a new Group component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        array $classes = [],
        ?array $blockTemplate = null,
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function transform(array $args): array
    {
        if (! empty($args['image'])) {
            $args['image'] = Image::make(...array_merge($args['image'], ['size' => 'geum_super']));

            $args['classes'][] = 'has-background';
            $args['classes'][] = 'has-background-image';

            if (! empty($args['background_color'])) {
                $args['attributes']['style']['--group--overlay--color'] = sprintf(
                    'var(--color-%s)',
                    $args['background_color']
                );
            }

            if (! empty($args['image_overlay_opacity'])) {
                $args['attributes']['style']['--group--overlay--opacity'] = $args['image_overlay_opacity'] / 100;
            }
        }

        $args['allowedBlocks'] = [
            'gravityforms/form',
            'acf/accordion',
            'core/paragraph',
            'core/image',
            'core/heading',
            'core/gallery',
            'core/list',
            'core/list-item',
            'core/quote',
            'core/button',
            'core/buttons',
            'core/freeform',
            'core/column',
            'core/columns',
            'core/embed',
            'core/html',
            'core/missing',
            'core/block',
            'core/separator',
            'core/shortcode',
            'core/table',
        ];

        return $args;
    }
}
