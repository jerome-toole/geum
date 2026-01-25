<?php

namespace Geum\Components;

use Geum\Component;
use Geum\ComponentBase;

/**
 * AnnouncementBanner Component
 *
 * Usage:
 *   use Geum\Components\AnnouncementBanner;
 *
 *   echo AnnouncementBanner::make();
 */
class AnnouncementBanner extends ComponentBase
{
    protected static string $name = 'announcement-banner';

    /**
     * Create a new AnnouncementBanner component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        ?bool $show = null,
        mixed $image = null,
        ?string $message = null,
        mixed $image_height = null,
        array $classes = [],
        mixed $background_color = null,
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    /**
     * Validate args before rendering.
     */
    protected static function validate(array $args): bool
    {
        $show = $args['show'] ?? get_field('show', 'option');

        return ! empty($show);
    }

    /**
     * Transform args before rendering.
     */
    protected static function transform(array $args): array
    {
        // Default ACF field values
        $args['show'] ??= get_field('show', 'option');
        $args['image'] ??= get_field('image', 'option');
        $args['message'] ??= get_field('message', 'option');
        $args['image_height'] ??= get_field('image_height', 'option');
        $args['background_color'] ??= get_field('theme_background_color', 'option');

        $args['attributes']['data-header-offset'] = '';

        return $args;
    }
}
