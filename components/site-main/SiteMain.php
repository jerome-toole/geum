<?php

namespace Geum\Components;

use Geum\Component;
use Geum\ComponentBase;

/**
 * SiteMain Component
 *
 * Usage:
 *   site_main_open(object: $post, classes: ['custom']);
 *   // ... content ...
 *   site_main_close();
 */
class SiteMain extends ComponentBase
{
    protected static string $name = 'site-main';

    private static ?array $openArgs = null;

    protected static function getDefaults(): array
    {
        return [
            'inner_el' => 'div',
            'attributes' => [],
            'blocks_context' => true,
        ];
    }

    /**
     * Output opening SiteMain markup.
     */
    public static function open(
        array $classes = [],
        ?object $object = null,
        ?string $inner_el = null,
        array $attributes = [],
        bool $blocks_context = true,
    ): string {
        $args = compact('classes', 'object', 'inner_el', 'attributes', 'blocks_context');
        $args = array_merge(static::getDefaults(), array_filter($args, fn ($v) => $v !== null && $v !== []));

        // WP_Post → article wrapper
        if (! empty($args['object']) && $args['object'] instanceof \WP_Post) {
            $args['inner_el'] = 'article';
        }

        // Default id
        if (empty($args['attributes']['id'])) {
            $args['attributes']['id'] = 'main';
        }

        static::$openArgs = $args;

        ob_start();
        include __DIR__.'/open.php';

        return ob_get_clean();
    }

    /**
     * Output closing SiteMain markup.
     */
    public static function close(): string
    {
        $args = static::$openArgs;
        static::$openArgs = null;

        ob_start();
        include __DIR__.'/close.php';

        return ob_get_clean();
    }

    /**
     * @deprecated Use site_main_open()/site_main_close() instead.
     */
    public static function make(
        array $classes = [],
        ?string $inner_el = null,
        bool $blocks_context = true,
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function transform(array $args): array
    {
        if (! empty($args['object'])) {
            if ($args['object'] instanceof \WP_Post) {
                $args['inner_el'] = 'article';
            }

            if (! has_block('acf/page-header')) {
                $args['header'] = \Geum\Components\PageHeader::make(
                    object: $args['object'],
                );
            }
        }

        if (empty($args['id']) && empty($args['attributes']['id'])) {
            $args['attributes']['id'] = 'main';
        }

        return $args;
    }
}
