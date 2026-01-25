<?php

namespace Geum\Components;

use Geum\Component;
use Geum\ComponentBase;

/**
 * SkipLink Component
 *
 * Usage:
 *   use Geum\Components\SkipLink;
 *
 *   echo SkipLink::make();
 */
class SkipLink extends ComponentBase
{
    protected static string $name = 'skip-link';

    /**
     * Create a new SkipLink component.
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
