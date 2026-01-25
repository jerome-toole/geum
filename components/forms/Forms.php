<?php

namespace Geum\Components;

use Geum\Component;
use Geum\ComponentBase;

/**
 * Forms Component
 *
 * Usage:
 *   use Geum\Components\Forms;
 *
 *   echo Forms::make();
 */
class Forms extends ComponentBase
{
    protected static string $name = 'forms';

    /**
     * Create a new Forms component.
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
