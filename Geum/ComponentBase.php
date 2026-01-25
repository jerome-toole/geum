<?php

namespace Geum;

/**
 * Base class for typed component classes.
 *
 * Extend this class to create components with typed ::make() factory methods.
 *
 * Usage:
 *   echo Accordion::make(title: 'FAQ', open: true);
 *   echo Card::make(object: $post, show_read_more: false);
 */
abstract class ComponentBase
{
    /**
     * The component name (e.g., 'accordion', 'card').
     * Override in child classes.
     */
    protected static string $name = '';

    /**
     * The Component's file path, relative to the theme root directory.
     */
    public string $path;

    /**
     * The Component's data and settings.
     */
    public ?array $args = [];

    /**
     * The current stack of components. Used to track nested/descendant components' ancestors.
     */
    protected static array $componentStack = [];

    /**
     * The Component's parent, if one exists.
     */
    public ?self $parent;

    /**
     * Create a new component instance.
     *
     * @param  array  $args  The processed arguments for the component.
     */
    public function __construct(array $args)
    {
        $this->path = 'components/'.static::$name;
        $this->parent = static::getCurrentComponent();
        $this->args = $args;
    }

    /**
     * Magic getter - access $args as properties.
     * Real typed properties take precedence when added later.
     */
    public function __get(string $name): mixed
    {
        return $this->args[$name] ?? null;
    }

    /**
     * Magic isset - check if arg exists.
     */
    public function __isset(string $name): bool
    {
        return isset($this->args[$name]);
    }

    /**
     * Helper to merge variadic args with defined vars.
     * Handles the ...$others pattern in component make() methods.
     *
     * @param  array  $definedVars  Result from get_defined_vars().
     * @return array The merged arguments.
     */
    protected static function mergeArgs(array $definedVars): array
    {
        if (isset($definedVars['others'])) {
            $args = array_merge($definedVars['others'], $definedVars);
            unset($args['others']);

            return $args;
        }

        return $definedVars;
    }

    /**
     * Default values for component args. Override in child classes.
     * Applied before transform() for both make() and fromBlock() calls.
     */
    protected static function getDefaults(): array
    {
        return [];
    }

    /**
     * Merge defaults into args. Null values are replaced with defaults.
     */
    protected static function applyDefaults(array $args): array
    {
        foreach (static::getDefaults() as $key => $default) {
            if (! array_key_exists($key, $args) || $args[$key] === null) {
                $args[$key] = $default;
            }
        }

        return $args;
    }

    /**
     * Validate args before rendering. Override in child classes.
     * Return false to prevent the component from rendering.
     */
    protected static function validate(array $args): bool
    {
        return true;
    }

    /**
     * Transform args before rendering. Override in child classes.
     */
    protected static function transform(array $args): array
    {
        return $args;
    }

    /**
     * Process args through the filter chain.
     * Called by child ::make() methods.
     *
     * @param  array  $args  The raw arguments from ::make().
     * @return array|null The processed arguments, or null to skip rendering.
     */
    protected static function processArgs(array $args): ?array
    {
        $args = static::applyDefaults($args);

        if (! static::validate($args)) {
            return null;
        }

        $args = static::transform($args);

        // External filtering via WordPress hooks (still available)
        $args = \apply_filters('geum/component/before_filters', $args);
        $args = \apply_filters('geum/component/'.static::$name, $args);
        $args = \apply_filters('geum/component/after_filters', $args);

        return $args;
    }

    /**
     * Factory helper for child ::make() methods.
     */
    protected static function createFromArgs(array $args): ?static
    {
        $args = static::processArgs($args);

        return $args === null ? null : new static($args);
    }

    /**
     * Create component from ACF block data.
     *
     * @param  array  $block  The ACF block array.
     * @param  mixed  $acf_fields  The ACF field values.
     * @param  string|null  $content  Inner block content.
     * @param  bool  $is_preview  Whether this is a preview render.
     * @param  int|null  $post_id  The current post ID.
     */
    public static function fromBlock(
        array $block,
        mixed $acf_fields = null,
        ?string $content = null,
        bool $is_preview = false,
        ?int $post_id = null
    ): ?static {
        $args = Component::generateArgsFromBlock($block, $acf_fields, $content, $is_preview, $post_id);

        // Merge ACF fields into args
        if (is_array($acf_fields)) {
            $args = array_merge($acf_fields, $args);
        }

        return static::makeFromArgs($args);
    }

    /**
     * Factory method for creating components from an args array.
     * Used by Component::get() for backwards compatibility and by fromBlock().
     *
     * @param  array  $args  The arguments to process.
     */
    public static function makeFromArgs(array $args): ?static
    {
        return static::createFromArgs($args);
    }

    /**
     * Returns the component's template output.
     */
    public function __toString(): string
    {
        $args = $this->args;

        // Bail early - args have been nulled, don't output component.
        if ($args === null) {
            return '';
        }

        if (empty($this->parent)) {
            $this->parent = static::getCurrentComponent();
        }

        // Push the current Component onto the stack.
        static::pushComponent($this);

        ob_start();

        \do_action('geum/component/before', $this->path, $args, $this);

        require \Geum\Paths::resolve($this->path); // Render the component.

        \do_action('geum/component/after', $this->path, $args, $this);

        // Pop the current component off the stack.
        static::popComponent();

        return ob_get_clean();
    }

    /**
     * Retrieve the current Component from the stack.
     */
    public static function getCurrentComponent(): ?self
    {
        if (empty(static::$componentStack)) {
            return null;
        }

        return static::$componentStack[array_key_last(static::$componentStack)];
    }

    protected static function pushComponent(self $component): void
    {
        static::$componentStack[] = $component;
    }

    protected static function popComponent(): ?self
    {
        return array_pop(static::$componentStack);
    }

    /**
     * Get the component name.
     */
    public static function getName(): string
    {
        return static::$name;
    }
}
