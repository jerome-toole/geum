<?php

namespace Geum\Components;

use Geum\Component;
use Geum\ComponentBase;

/**
 * NoContent Component
 *
 * Usage:
 *   use Geum\Components\NoContent;
 *
 *   echo NoContent::make();
 */
class NoContent extends ComponentBase
{
    protected static string $name = 'no-content';

    protected static function getDefaults(): array
    {
        return [
            'content' => ['message' => __('No content found.', 'geum')],
        ];
    }

    /**
     * Create a new NoContent component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        array $classes = [],
        ?array $content = null,
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    /**
     * Transform args before rendering.
     */
    protected static function transform(array $args): array
    {
        if (! empty($args['object'])) {
            $object = $args['object'];

            if ($object instanceof \WP_Query && $object->is_404()) {
                $args['content']['message'] = __("It seems we can't find what you're looking for.", 'geum');
            } elseif ($object instanceof \WP_Query && $object->is_search()) {
                $args['content']['message'] = __(
                    'Sorry, but nothing matched your search terms. Please try again with some different keywords.',
                    'geum'
                );
            }
        } elseif (is_admin()) {
            $args['content']['message'] = __('Items cannot be displayed in the editor.', 'geum');
        }

        return $args;
    }
}
