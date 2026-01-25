<?php

namespace Geum\Components;

use Geum\Component;
use Geum\ComponentBase;

/**
 * PageHeader Component
 *
 * Usage:
 *   use Geum\Components\PageHeader;
 *
 *   echo PageHeader::make();
 */
class PageHeader extends ComponentBase
{
    protected static string $name = 'page-header';

    protected static function getDefaults(): array
    {
        return [
            'image_position' => 'inset',
            'background' => 'brand-1',
            'show_breadcrumbs' => true,
        ];
    }

    /**
     * Create a new PageHeader component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        ?object $object = null,
        array $classes = [],
        ?string $type = null,
        ?string $image_position = null,
        ?string $background = null,
        array $attributes = [],
        ?bool $show_breadcrumbs = null,
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function transform(array $args): array
    {
        if (isset($args['is_preview']) && $args['is_preview']) {
            $args['object'] = \get_post($args['post_id']);
        } else {
            $args['object'] = \Geum\WordPress\PageObject::get() ?? null;
        }

        $heading = '';

        if (! empty($args['object'])) {
            $object = $args['object'];

            if ($object instanceof \WP_Term) {
                $heading = $object->name;
            } elseif ($object instanceof \WP_Post_Type) {
                if ($routerPage = \Geum\Router::getPage()) {
                    $object = $routerPage;
                } else {
                    $heading = $object->label;
                }
            } elseif ($object instanceof \WP_Query && $object->is_404()) {
                $heading = __('404', 'geum');
            } elseif ($object instanceof \WP_Query && $object->is_search()) {
                $heading = __('Search', 'geum');

                if (! empty($object->query['s'])) {
                    $args['subheading'] = sprintf(__("Showing results for '%s'", 'geum'), $object->query['s']);
                }
            } elseif ($object instanceof \WP_User) {
                $heading = sprintf(__('Posts by %s', 'geum'), $object->data->display_name);
            }

            if ($object instanceof \WP_Post) {
                $heading = $object->post_title;
                $args['image'] = \get_post_thumbnail_id($object);

                if ($object->post_type === 'post') {
                    $args['meta'] = sprintf(__('Published on %s ', 'geum'), \get_the_date(\get_option('date_format'), $object->ID));
                    $args['labels'] = \Theme\Utils\ObjectMeta::getObjectLabels($object->ID, ['limit' => 3, 'taxonomies' => ['category']]);
                    $args['background'] = false;
                    $args['image_position'] = 'mini';
                    $args['type'] = 'article';

                    if ($author_name = \get_the_author_meta('display_name', $object->post_author)) {
                        $args['meta'] .= sprintf(__('by %s ', 'geum'), $author_name);
                    }
                } elseif ($object->post_type === 'page') {
                    if (\is_front_page()) {
                        $args['classes'][] = 'page-header--home';
                        $args['show_breadcrumbs'] = false;
                    }

                    if (empty($object->post_parent)) {
                        $args['show_breadcrumbs'] = false;
                    }
                }

                if ($heading === 'Auto Draft') {
                    $heading = __('Post Title', 'geum');
                }

                unset($args['object']);
            }
        }

        $args['heading'] = $args['heading'] ?? $heading;

        if (! empty($heading) && empty($args['heading'])) {
            $args['heading'] = $heading;
        }

        if (! empty($args['primary_call_to_action'])) {
            $args['buttons'][] = array_merge($args['primary_call_to_action'], ['classes' => ['btn']]);
        }

        if (isset($args['is_preview']) && $args['is_preview']) {
            if (empty($args['heading'])) {
                $args['heading'] = _x('Add title', 'Placeholder for page header title', 'geum');
            }

            if (empty($args['subheading'])) {
                $args['subheading'] = _x('Add subheading', 'Placeholder for page header subheading', 'geum');
            }
        }

        if (! empty($args['image'])) {
            if (is_array($args['image'])) {
                $args['image'] = $args['image']['ID'];
            }

            if (($args['image_position'] ?? '') === 'mini') {
                $args['image'] = Image::make(
                    id: $args['image'],
                    size: 'medium',
                    sizes: '(min-width: 768px) 50vw, 100vw',
                );
                $args['classes'][] = 'has-mini-image';
            } else {
                $args['image'] = Image::make(
                    id: $args['image'],
                    size: 'geum_super',
                    sizes: '(min-width: 768px) 50vw, 100vw',
                );
                $args['classes'][] = 'has-inset-image';
            }
        }

        if (! empty($args['heading'])) {
            $args['heading'] = [
                'heading' => $args['heading'],
                'el' => 'h1',
                'classes' => ['page-header__heading', 'is-style-type-h1'],
            ];
        }

        if (! empty($args['background']) && $args['background'] !== 'none') {
            $args['classes'][] = 'has-'.$args['background'].'-background-color';
            $args['classes'][] = 'has-background';
        }

        if (! empty($args['type'])) {
            $args['classes'][] = 'page-header--type--'.$args['type'];
        }

        if (! empty($args['image_overlay_opacity'])) {
            $args['attributes']['style']['--page-header--overlay-opacity'] = $args['image_overlay_opacity'].'%';
        }

        if (! empty($args['show_breadcrumbs'])) {
            $args['classes'][] = 'has-breadcrumbs';
        }

        return $args;
    }
}
