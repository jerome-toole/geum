<?php

namespace Geum\Components;

use Geum\Component;
use Geum\ComponentBase;

/**
 * Heading Component
 *
 * Usage:
 *   use Geum\Components\Heading;
 *
 *   echo Heading::make();
 */
class Heading extends ComponentBase
{
    protected static string $name = 'heading';

    protected static function getDefaults(): array
    {
        return [
            'el' => 'h2',
        ];
    }

    /**
     * Create a new Heading component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        ?string $el = null,
        ?string $heading = null,
        mixed $id = null,
        mixed $link = null,
        mixed $target = null,
        ?array $attributes = [],
        ?array $classes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function transform(array $args): array
    {
        $args['classes'] = array_merge(['heading'], $args['classes'] ?? []);

        if (! empty($args['link'])) {
            $args['heading'] = Link::make(
                url: $args['link'],
                title: $args['heading'],
                target: $args['target'] ?? '',
            );
        }

        $args['content'] = $args['heading'];

        if (! empty($args['id'])) {
            $args['attributes']['id'] = $args['id'];
        }

        return $args;
    }
}
