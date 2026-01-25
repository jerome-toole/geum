<?php

namespace Geum\Components;

use Geum\Component;
use Geum\ComponentBase;

/**
 * SocialIcons Component
 *
 * Usage:
 *   use Geum\Components\SocialIcons;
 *
 *   echo SocialIcons::make();
 */
class SocialIcons extends ComponentBase
{
    protected static string $name = 'social-icons';

    protected static function getDefaults(): array
    {
        return [
            'show' => true,
        ];
    }

    /**
     * Create a new SocialIcons component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        array $classes = [],
        ?string $title = null,
        mixed $networks = null,
        ?bool $show = null,
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    /**
     * Validate args before rendering.
     */
    protected static function validate(array $args): bool
    {
        $networks = $args['networks'] ?? get_field('social_networks', 'option');

        return $args['show'] !== false && ! empty($networks) && is_array($networks);
    }

    /**
     * Transform args before rendering.
     */
    protected static function transform(array $args): array
    {
        $args['networks'] ??= get_field('social_networks', 'option');

        foreach ($args['networks'] as $key => $network) {
            $title = $args['networks'][$key]['network']['label'];

            // 'title' should be a formatted string with a network name placeholder.
            if (! empty($args['title']) && strpos($args['title'], '%s') !== false) {
                $title = sprintf(
                    $args['title'],
                    $title
                );
            }

            $args['networks'][$key]['title'] = $title;
        }

        return $args;
    }
}
