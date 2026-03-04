<?php

namespace Geum\Components;

use Geum\Component;
use Geum\ComponentBase;

/**
 * SiteMain Component
 *
 * Usage:
 *   SiteMain::open(classes: ['custom'], object: $post);
 *   // ... content ...
 *   SiteMain::close();
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
            'content_flow' => true,
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
        bool $content_flow = true,
    ): string {
        $args = compact('classes', 'object', 'inner_el', 'attributes', 'content_flow');
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
}
