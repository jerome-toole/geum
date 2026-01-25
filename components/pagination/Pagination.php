<?php

namespace Geum\Components;

use Geum\Component;
use Geum\ComponentBase;

/**
 * Pagination Component
 *
 * Usage:
 *   use Geum\Components\Pagination;
 *
 *   echo Pagination::make();
 */
class Pagination extends ComponentBase
{
    protected static string $name = 'pagination';

    protected static function getDefaults(): array
    {
        return [];
    }

    /**
     * Create a new Pagination component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        ?array $classes = null,
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    /**
     * Transform args before rendering.
     */
    protected static function transform(array $args): array
    {
        $args['output'] = get_the_posts_pagination([
            'prev_text' => __('Previous page', 'geum'),
            'next_text' => __('Next page', 'geum'),
            'before_page_number' => '<span class="screen-reader-text">'.__('Page', 'geum').' </span>',
            'class' => 'pagination__inner',
        ]);

        return $args;
    }
}
