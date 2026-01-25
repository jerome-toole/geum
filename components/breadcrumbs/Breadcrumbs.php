<?php

namespace Geum\Components;

use Geum\Component;
use Geum\ComponentBase;

/**
 * Breadcrumbs Component
 *
 * Usage:
 *   use Geum\Components\Breadcrumbs;
 *
 *   echo Breadcrumbs::make();
 */
class Breadcrumbs extends ComponentBase
{
    protected static string $name = 'breadcrumbs';

    /**
     * Create a new Breadcrumbs component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        array $classes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

}
