<?php

namespace Geum\Components;

use Geum\Component;
use Geum\ComponentBase;

/**
 * Animate Component
 *
 * Usage:
 *   use Geum\Components\Animate;
 *
 *   echo Animate::make();
 */
class Animate extends ComponentBase
{
    protected static string $name = 'animate';

    /**
     * Create a new Animate component.
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
