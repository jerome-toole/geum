<?php

namespace Geum\Components;

use Geum\Component;
use Geum\ComponentBase;

/**
 * Button Component
 *
 * Usage:
 *   use Geum\Components\Button;
 *
 *   echo Button::make();
 */
class Button extends ComponentBase
{
    protected static string $name = 'button';

    /**
     * Create a new Button component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        string $content = '',
        array $classes = [],
        bool $screen_reader_text = false,
        string $type = 'button',
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function validate(array $args): bool
    {
        return ! empty($args['content']);
    }

    protected static function transform(array $args): array
    {
        if (! empty($args['screen_reader_text'])) {
            $args['content'] = Element::make(
                el: 'span',
                content: $args['content'],
                classes: ['screen-reader-text'],
            );
        }

        $args['attributes']['type'] = $args['type'];

        return $args;
    }
}
