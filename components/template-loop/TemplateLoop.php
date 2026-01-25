<?php

namespace Geum\Components;

use Geum\Component;
use Geum\ComponentBase;

/**
 * TemplateLoop Component
 *
 * Usage:
 *   use Geum\Components\TemplateLoop;
 *
 *   echo TemplateLoop::make();
 */
class TemplateLoop extends ComponentBase
{
    protected static string $name = 'template-loop';

    protected static function getDefaults(): array
    {
        return [
            'items_render_component' => 'cards',
            'show_taxonomy_filters' => true,
        ];
    }

    /**
     * Create a new TemplateLoop component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        array $classes = [],
        array $items = [],
        mixed $object = null,
        ?string $items_render_component = null,
        array $items_render_component_args = [],
        ?bool $show_taxonomy_filters = null,
        ...$others
    ): ?static {
        $object ??= \Geum\WordPress\PageObject::get();

        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    /**
     * Transform args before rendering.
     */
    protected static function transform(array $args): array
    {
        while (\have_posts()) {
            \the_post();
            $args['items'][]['object'] = \get_post();
        }

        $args['items_render_component_args']['items'] = $args['items'];

        return $args;
    }
}
