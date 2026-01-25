<?php

namespace Geum\Components;

use Geum\Component;
use Geum\ComponentBase;

/**
 * TaxonomyFilters Component
 *
 * Usage:
 *   use Geum\Components\TaxonomyFilters;
 *
 *   echo TaxonomyFilters::make();
 */
class TaxonomyFilters extends ComponentBase
{
    protected static string $name = 'taxonomy-filters';

    protected static function getDefaults(): array
    {
        return [
            'show' => true,
        ];
    }

    /**
     * Create a new TaxonomyFilters component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        array $classes = [],
        ?int $current_item = null,
        ?string $label = null,
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
        return $args['show'] !== false;
    }

    /**
     * Transform args before rendering.
     */
    protected static function transform(array $args): array
    {
        // Default translated value
        $args['label'] ??= __('Filter by', 'geum');

        if (! empty($args['object'])) {
            $object = $args['object'];

            if ($object instanceof \WP_Term) {
                $args['taxonomy'] = $object->taxonomy;
                $args['current_item'] = $object->term_id;
            } elseif ($object instanceof \WP_Post_Type) {
                $args['taxonomy'] = 'category';

                if ($object->name === 'post') {
                    $args['taxonomy'] = 'category';
                } elseif ($object->name === 'event') {
                    $args['taxonomy'] = 'location';
                }
            }
        }

        if (! empty($args['taxonomy'])) {
            $tax = get_taxonomy($args['taxonomy']);

            $args['label'] = sprintf(
                __('Filter by %s', 'geum'),
                strtolower($tax->labels->singular_name)
            );

            $items = get_terms($args['taxonomy']);

            if (! empty($items)) {
                foreach ($items as $key => $item) {
                    $args['items'][$key] = [
                        'title' => $item->name,
                        'url' => get_term_link($item->slug, $item->taxonomy),
                        'classes' => [
                            'btn',
                            'btn--small',
                            'taxonomy-filters__item',
                        ],
                    ];

                    if ($args['current_item'] === $item->term_id) {
                        $args['items'][$key]['classes'][] = 'taxonomy-filters__item--current';
                    }
                }
            }
        }

        return $args;
    }
}
