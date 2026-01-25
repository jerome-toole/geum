<?php

namespace Geum\Components;

use Geum\Component;
use Geum\ComponentBase;

/**
 * Link Component
 *
 * Usage:
 *   use Geum\Components\Link;
 *
 *   echo Link::make();
 */
class Link extends ComponentBase
{
    protected static string $name = 'link';

    protected static function getDefaults(): array
    {
        return [
            'content_filter' => 'wp_kses_post',
        ];
    }

    /**
     * Create a new Link component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        ?string $title = null,
        ?string $url = null,
        ?string $target = null,
        array $attributes = [],
        array $classes = [],
        ?string $prefix = null,
        ?string $suffix = null,
        ?string $assistive_text_before = null,
        ?string $assistive_text_after = null,
        ?string $content_filter = null,
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function transform(array $args): array
    {
        $args['content'] = $args['content'] ?? $args['title'];

        if (! empty($args['assistive_text_before'])) {
            $args['content'] = Element::make(
                el: 'span',
                content: $args['assistive_text_before'],
                classes: ['screen-reader-text'],
            ).$args['content'];
        }

        if (! empty($args['prefix'])) {
            $args['content'] = $args['prefix'].$args['content'];
        }

        if (! empty($args['assistive_text_after'])) {
            $args['content'] .= Element::make(
                el: 'span',
                content: $args['assistive_text_after'],
                classes: ['screen-reader-text'],
            );
        }

        if (! empty($args['suffix'])) {
            $args['content'] .= $args['suffix'];
        }

        if (! empty($args['url'])) {
            $args['attributes']['href'] = esc_url($args['url']);
        }

        if (! empty($args['target'])) {
            $args['attributes']['target'] = $args['target'];

            if ($args['target'] === '_blank') {
                $args['attributes']['rel'][] = 'noopener';
            }
        }

        if (empty($args['content_filter'])) {
            $args['content_filter'] = fn ($content) => $content;
        }

        return $args;
    }
}
