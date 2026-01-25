<?php

namespace Geum\Components;

use Geum\Component;
use Geum\ComponentBase;

/**
 * VideoItem Component
 *
 * Usage:
 *   use Geum\Components\VideoItem;
 *
 *   echo VideoItem::make();
 */
class VideoItem extends ComponentBase
{
    protected static string $name = 'video-item';

    /**
     * Create a new VideoItem component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        array $classes = [],
        string $heading = '',
        string $content = '',
        string $video = '',
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    /**
     * Transform args before rendering.
     */
    protected static function transform(array $args): array
    {
        if (! empty($args['image'])) {
            $args['image']['size'] = 'large';
        }

        if (! empty($args['video'])) {
            // Use preg_match to find iframe src.
            preg_match('/src="(.+?)"/', $args['video'], $matches);
            $src = $matches[1];

            // Add extra parameters to src and replace HTML.
            $params = [
                'autoplay' => 1,
                'modestbranding' => 1,
                'rel' => 0, // Whether to show videos from the same channel upon completion.
            ];

            $args['video'] = str_replace(
                $src,
                add_query_arg($params, $src),
                $args['video']
            );
        }

        $args['video'] = str_replace(' src=', ' data-src=', $args['video']);

        return $args;
    }
}
