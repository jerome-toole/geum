<?php

namespace Geum\Components;

use Geum\Component;
use Geum\ComponentBase;

/**
 * Editor Component
 *
 * Usage:
 *   use Geum\Components\Editor;
 *
 *   echo Editor::make();
 */
class Editor extends ComponentBase
{
    protected static string $name = 'editor';

    /**
     * Create a new Editor component.
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
