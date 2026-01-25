<?php

namespace Geum\Components;

use Geum\Component;
use Geum\ComponentBase;

/**
 * CoreBlocks Component
 *
 * Usage:
 *   use Geum\Components\CoreBlocks;
 *
 *   echo CoreBlocks::make();
 */
class CoreBlocks extends ComponentBase
{
    protected static string $name = 'core-blocks';

    /**
     * Create a new CoreBlocks component.
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
