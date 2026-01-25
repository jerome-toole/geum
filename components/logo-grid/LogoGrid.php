<?php

namespace Geum\Components;

use Geum\Component;
use Geum\ComponentBase;

/**
 * LogoGrid Component
 *
 * Usage:
 *   use Geum\Components\LogoGrid;
 *
 *   echo LogoGrid::make();
 */
class LogoGrid extends ComponentBase
{
    protected static string $name = 'logo-grid';

    protected static function getDefaults(): array
    {
        return [
            'display' => 'grid',
        ];
    }

    /**
     * Create a new LogoGrid component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        array $classes = [],
        ?string $columns = null,
        ?string $display = null,
        array $items = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    /**
     * Transform args before rendering.
     */
    protected static function transform(array $args): array
    {
        $args['classes'][] = 'logo-grid--'.$args['display'];

        // Generate items array.
        if (! empty($args['logos'])) {
            foreach ($args['logos'] as $logo) {
                if (! empty($logo['image'])) {
                    $image = array_merge($logo['image'], [
                        'size' => 'medium',
                    ]);

                    $args['items'][] = [
                        'image' => $image,
                        'link' => $logo['link'] ?? null,
                    ];
                }
            }
            // Tidy up $args by removing logos.
            unset($args['logos']);
        }

        if (! empty($args['columns'])) {
            $args['classes'][] = 'cards--columns-'.$args['columns'];
        }

        if (! empty($args['background_color']) && $args['background_color'] !== 'none') {
            $args['classes'][] = 'has-'.$args['background_color'].'-background-color';
            $args['classes'][] = 'has-background';
        }

        return $args;
    }
}
