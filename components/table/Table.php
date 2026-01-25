<?php

namespace Geum\Components;

use Geum\Component;
use Geum\ComponentBase;

/**
 * Table Component
 *
 * Usage:
 *   use Geum\Components\Table;
 *
 *   echo Table::make();
 */
class Table extends ComponentBase
{
    protected static string $name = 'table';

    /**
     * Create a new Table component.
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
