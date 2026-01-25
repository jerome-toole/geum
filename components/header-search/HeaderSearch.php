<?php

namespace Geum\Components;

use Geum\Component;
use Geum\ComponentBase;

/**
 * HeaderSearch Component
 *
 * Usage:
 *   use Geum\Components\HeaderSearch;
 *
 *   echo HeaderSearch::make();
 */
class HeaderSearch extends ComponentBase
{
    protected static string $name = 'header-search';

    protected static function getDefaults(): array
    {
        return [];
    }

    /**
     * Create a new HeaderSearch component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        mixed $input_id = null,
        array $classes = [],
        ?string $background_color = null,
        array $attributes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    /**
     * Transform args before rendering.
     */
    protected static function transform(array $args): array
    {
        // Required classes
        $args['classes'] = array_merge([
            'header-search',
        ], $args['classes'] ?? []);

        // Default ACF field values
        $args['input_id'] ??= \wp_unique_id('header-search-');

        // ---------------------------------------
        // Default attributes.
        // ---------------------------------------
        $args['attributes'] = array_merge([
            'autocomplete' => 'off',
            'method' => 'get',
            'placeholder' => 'Search...',
            'action' => esc_url(home_url('/')),
            'role' => 'search',
        ], $args['attributes']);

        return $args;
    }
}
