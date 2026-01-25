<?php

namespace Geum\Components;

use Geum\Component;
use Geum\ComponentBase;

/**
 * Card Component
 *
 * Usage:
 *   use Geum\Components\Card;
 *
 *   echo Card::make();
 */
class Card extends ComponentBase
{
    protected static string $name = 'card';

    protected static function getDefaults(): array
    {
        return [
            'background' => 'white',
            'image_size' => 'medium_large',
            'show_read_more' => true,
            'heading_class' => 'is-style-type-h4',
        ];
    }

    /**
     * Create a new Card component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        array $attributes = [],
        array $classes = [],
        ?string $type = null,
        ?string $background = null,
        ?string $image_size = null,
        ?bool $show_read_more = null,
        ?string $heading_class = null,
        array $content = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function transform(array $args): array
    {
        $args['classes'] ??= [];

        if (! empty($args['object'])) {
            $object = $args['object'];

            if ($args['object'] instanceof \WP_Post) {
                $args['content'] = [
                    'heading' => get_the_title($object->ID),
                    'url' => get_the_permalink($object->ID),
                    'text' => get_the_excerpt($object->ID),
                    'meta' => '',
                    'labels' => \Theme\Utils\ObjectMeta::getObjectLabels($object->ID, [
                        'limit' => 1,
                        'taxonomies' => ['category'],
                    ]),
                ];

                if (has_post_thumbnail($object->ID)) {
                    $args['content']['image'] = ['ID' => get_post_thumbnail_id($object->ID)];
                }

                if (! has_excerpt($object->ID)) {
                    if ($page_header_content = get_field('page_header_content', $object->ID)) {
                        $args['content']['text'] = $page_header_content;
                    }
                }

                if ($object->post_type === 'post') {
                    $args['type'] = 'article';
                    $args['show_read_more'] = false;
                    $args['content']['text'] = '';
                    $args['heading_class'] = 'is-style-type-h4';

                    $metaDate = \Theme\Utils\ObjectMeta::getObjectDate($object);
                    $metaAuthor = \Theme\Utils\ObjectMeta::getObjectAuthor($object);

                    $args['content']['meta'] .= $metaDate ?? null;
                    $args['content']['meta'] .= $metaDate && $metaAuthor ? ' ' : null;

                    if (! empty($metaAuthor)) {
                        $metaAuthor = Link::make(...$metaAuthor);
                        $args['content']['meta'] .= sprintf(__('by %s', 'geum'), $metaAuthor);
                    }
                }
            } elseif ($args['object'] instanceof \WP_Term) {
                $args['content'] = [
                    'heading' => $object->name,
                    'url' => get_term_link($object->ID),
                    'text' => $object->description,
                ];
            }

            if (! empty($args['content']['url']) && empty($args['content']['read_more']['url'])) {
                $args['content']['read_more']['url'] = $args['content']['url'];
            }

            if (empty($args['content']['read_more']['title'])) {
                $args['content']['read_more']['title'] = __('Read more', 'geum');
            }
        } elseif (! empty($args['content'])) {
            $content = $args['content'];

            if (! empty($content['link'])) {
                $content['url'] = $content['link']['url'];
                $content['read_more']['url'] = $content['link']['url'];

                if (empty($content['link']['title'])) {
                    $args['show_read_more'] = false;
                } else {
                    $content['read_more']['title'] = $content['link']['title'];
                }
            }

            $args['content'] = $content;
        }

        if (! empty($args['content']['read_more'])) {
            $args['content']['read_more'] = array_merge([
                'classes' => ['btn', 'g-card__read-more'],
            ], $args['content']['read_more']);
        }

        if (! empty($args['image_fit'])) {
            $args['attributes']['style']['--g-card--image--object-fit'] = $args['image_fit'];
        }

        if (! empty($args['background']) && $args['background'] !== 'none') {
            $args['classes'][] = 'has-'.$args['background'].'-background-color';
            $args['classes'][] = 'has-background';
        }

        $args['content']['heading'] = [
            'heading' => $args['content']['heading'],
            'classes' => ['g-card__heading'],
        ];

        if (! empty($args['content']['url'])) {
            $args['content']['heading']['link'] = $args['content']['url'];
        }

        if (! empty($args['heading_class'])) {
            $args['content']['heading']['classes'][] = $args['heading_class'];
        }

        if (! empty($args['content']['image'])) {
            $args['content']['image']['size'] = $args['image_size'];
        }

        $args['classes'][] = ! empty($args['content']['image']) ? 'has-image' : null;
        $args['classes'][] = ! empty($args['content']['url']) ? 'has-link' : null;

        if (! empty($args['type'])) {
            $args['classes'][] = 'g-card--type--'.$args['type'];
        }

        return $args;
    }
}
