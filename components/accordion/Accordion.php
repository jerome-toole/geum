<?php

namespace Geum\Components;

use Geum\Component;
use Geum\ComponentBase;

/**
 * Accordion Component
 *
 * Usage:
 *   use Geum\Components\Accordion;
 *
 *   echo Accordion::make();
 */
class Accordion extends ComponentBase
{
    protected static string $name = 'accordion';

    /**
     * Create a new Accordion component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        array $classes = [],
        string $content = '',
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function transform(array $args): array
    {
        if (! empty($args['heading'])) {
            $args['heading'] = [
                'heading' => $args['heading'],
                'classes' => ['accordion__heading'],
            ];
        }

        if (! empty($args['accordion_items'])) {
            $group_id = wp_unique_id('accordion-group-');

            $args['accordion_items'] = array_map(function ($item) use ($group_id) {
                $item['panel_id'] = wp_unique_id('accordion-panel-');
                $item['button_id'] = wp_unique_id('accordion-button-');

                $item['button'] = [
                    'id' => $item['button_id'],
                    'classes' => ['accordion__item__header'],
                    'attributes' => [
                        'aria-expanded' => 'false',
                        'aria-controls' => $item['panel_id'],
                    ],
                    'content' => Heading::make(
                        el: 'h3',
                        heading: $item['title'],
                        classes: ['accordion__item__heading'],
                    ),
                ];

                $item['panel_attributes'] = [
                    'id' => $item['panel_id'],
                    'class' => 'accordion__item__panel',
                    'hidden' => true,
                    'aria-hidden' => 'true',
                    'aria-labelledby' => $item['button_id'],
                    'data-disclosure' => true,
                    'data-disclosure-animate' => 'true',
                    'data-disclosure-group' => $group_id,
                ];

                return $item;
            }, $args['accordion_items']);
        }

        return $args;
    }
}
