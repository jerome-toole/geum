<?php

namespace Geum\Components;

use Geum\Component;
use Geum\ComponentBase;

/**
 * MediaContent Component
 *
 * Usage:
 *   use Geum\Components\MediaContent;
 *
 *   echo MediaContent::make();
 */
class MediaContent extends ComponentBase
{
    protected static string $name = 'media-content';

    protected static function getDefaults(): array
    {
        return [
            'media_type' => 'image',
            'media_side' => 'left',
        ];
    }

    /**
     * Create a new MediaContent component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        array $classes = [],
        ?string $heading = null,
        ?string $subheading = null,
        ?string $content = null,
        array $video = [],
        array $image = [],
        ?string $media_type = null,
        ?string $media_side = null,
        ?bool $reverse = null,
        ?string $media = null,
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    /**
     * Transform args before rendering.
     */
    protected static function transform(array $args): array
    {
        $args['classes'][] = 'media-content--'.$args['media_side'];
        $args['classes'][] = 'media-content--media-type--'.$args['media_type'];

        if (! empty($args['button_1'])) {
            $args['button_1']['classes'] = [
                'btn',
            ];
        }

        // -------------------------------------------------------------------------
        // Set image args if one exists.
        // -------------------------------------------------------------------------
        if (! empty($args['image'])) {
            $args['image']['size'] = 'super';
            $args['image']['sizes'] = '(max-width: 768px) 100vw, 50vw';
        }

        // -------------------------------------------------------------------------
        // Set video args if one exists.
        // -------------------------------------------------------------------------
        if (! empty($args['video'])) {
            $args['video'] = [
                'video' => $args['video'],
                'image' => $args['image'],
            ];
        }

        // -------------------------------------------------------------------------
        // Set media args as necessary.
        // -------------------------------------------------------------------------
        $type = $args['media_type'];
        if (! empty($type) && ! empty($args[$type])) {
            $args['media'] = $args[$type];
        }

        return $args;
    }
}
