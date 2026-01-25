<?php

namespace Geum;

/**
 * Component rendering and template resolution.
 *
 * This class handles:
 * - Template rendering via __toString()
 * - Component stack for nested components
 * - ACF block registration
 * - Asset enqueueing
 *
 * For creating components, use typed classes extending ComponentBase.
 *
 * @see \Geum\ComponentBase
 */
class Component
{
    /**
     * The Component's file path, relative to the theme root directory.
     */
    public string $path;

    /**
     * The Component's name.
     */
    public string $name;

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
    public ?Component $parent;

    /**
     * Magic getter - access $args as properties.
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
     * Constructor.
     *
     * @deprecated Use typed component classes with ::make() instead.
     *
     * @param  string  $name  The component's name.
     * @param  array  $args  The arguments to pass to the component.
     */
    public function __construct(string $name, $args = [])
    {
        $this->name = $name;
        $this->path = "components/$name";
        $this->parent = static::getCurrentComponent();

        // Push the current Component onto the stack.
        static::pushComponent($this);

        // Filter the `$args` data being used by the component for output.
        $args = \apply_filters('geum/component/before_filters', $args);
        $args = \apply_filters("geum/component/$name", $args);
        $args = \apply_filters('geum/component/after_filters', $args);

        $this->args = $args;

        // Pop the current component off the stack.
        static::popComponent();
    }

    /**
     * Initialise class to set up hooks and filters for all Components.
     */
    public static function init(): void
    {
        // Set class args for components.
        \add_filter('geum/component/after_filters', [__CLASS__, 'buildComponentClasses'], 10);

        // Set attribute args for components.
        \add_filter('geum/component/after_filters', [__CLASS__, 'buildComponentAttributes'], 20);

        // ACF block registration.
        \add_action('acf/init', [__CLASS__, 'addBlocks']);
        \add_filter('acf/settings/load_json', [__CLASS__, 'loadBlockFieldGroupJSON']);
        \add_action('acf/update_field_group', [__CLASS__, 'saveBlockFieldGroupJSON'], 1);

        // Asset enqueueing.
        \add_action('geum/component/before', [__CLASS__, 'enqueueScripts'], 10, 3);
        \add_action('geum/component/before', [__CLASS__, 'enqueueStyles'], 10, 3);
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
     * Retrieve the current Component.
     */
    public static function getCurrentComponent(): ?Component
    {
        if (empty(static::$componentStack)) {
            return null;
        }

        return static::$componentStack[array_key_last(static::$componentStack)];
    }

    protected static function pushComponent($component): void
    {
        static::$componentStack[] = $component;
    }

    protected static function popComponent(): ?Component
    {
        return array_pop(static::$componentStack);
    }

    public static function addBlocks(): void
    {
        self::requireFiles(
            glob(\get_theme_file_path('components/*/acf.php'))
        );
    }

    private static function requireFiles(array $files): void
    {
        $files = apply_filters('geum/component/before_require_files', $files);

        foreach ($files as $file) {
            require_once $file;
        }
    }

    public static function enqueueScripts(string $path, array $args, object $component): void
    {
        if (empty($component->name)) {
            return;
        }

        $jsPath = \Geum\Asset::extract("components/$component->name/$component->name.js");

        if (empty($jsPath)) {
            return;
        }

        if (! file_exists(\Geum\Paths::assetPath($jsPath))) {
            return;
        }

        \wp_enqueue_script(
            "$component->name-scripts",
            \Geum\Asset::URL($jsPath),
            \apply_filters("geum/component/$component->name/enqueue_script_dependencies", []),
            \apply_filters("geum/component/$component->name/enqueue_script_in_footer", false),
        );
    }

    public static function enqueueStyles(string $path, array $args, object $component): void
    {
        if (empty($component->name)) {
            return;
        }

        $cssPath = \Geum\Asset::extract("components/$component->name/$component->name.css");

        if (empty($cssPath)) {
            return;
        }

        if (! file_exists(\Geum\Paths::assetPath($cssPath))) {
            return;
        }

        \wp_enqueue_style(
            "$component->name-styles",
            \Geum\Asset::URL($cssPath),
            \apply_filters("geum/component/$component->name/enqueue_style_dependencies", []),
        );
    }

    /**
     * Load ACF block field groups from components' JSON files.
     */
    public static function loadBlockFieldGroupJSON(array $paths): array
    {
        return array_merge(
            $paths,
            glob(\get_theme_file_path('components/*'))
        );
    }

    /**
     * Save each ACF block's field group JSON file into their related components' directory.
     */
    public static function saveBlockFieldGroupJSON($group): void
    {
        if (empty($group['location'][0][0]['param']) || $group['location'][0][0]['param'] !== 'block') {
            return;
        }

        $blockName = str_replace('acf/', '', $group['location'][0][0]['value']);
        $blockFieldGroupJSONDirPaths = glob(\get_theme_file_path("components/$blockName"));

        if (! is_array($blockFieldGroupJSONDirPaths)) {
            return;
        }

        foreach ($blockFieldGroupJSONDirPaths as $path) {
            if (! is_dir($path)) {
                continue;
            }

            add_action('acf/settings/save_json', function () use ($path) {
                return $path;
            }, 9999);
        }
    }

    /**
     * Generate args for a Component from block attributes and ACF fields.
     */
    public static function generateArgsFromBlock(array $block, $acf_fields, $content = null, $is_preview = false, $post_id = null): array
    {
        $args = is_array($acf_fields) ? $acf_fields : [];

        $args['is_preview'] = $is_preview;
        $args['post_id'] = $post_id;

        if (! empty($block['anchor'])) {
            $args['anchor'] = $block['anchor'];
        }

        if (! empty($block['className'])) {
            $args['classes'] = [
                $block['className'],
            ];
        }

        if (! empty($args['anchor']) && empty($args['attributes']['id'])) {
            $args['attributes']['id'] = $args['anchor'];
        }

        if (! empty($block['align'])) {
            $args['align'] = $block['align'];
        }

        if (! empty($block['name'])) {
            $args['editor_block_name'] = $block['name'];
        }

        if (! empty($block['backgroundColor'])) {
            $args['background_color'] = $block['backgroundColor'];
        }

        if (! empty($block['theme_background_color'])) {
            $args['background_color'] = $block['theme_background_color'];
        }

        return $args;
    }

    /**
     * Adds common HTML classes based on the given Component args.
     */
    public static function buildComponentClasses(?array $args): ?array
    {
        if ($args === null) {
            return $args;
        }

        $args['classes'] = $args['classes'] ?? [];

        if (! empty($args['align'])) {
            $args['classes'][] = 'align'.$args['align'];
        }

        if (! empty($args['background_color']) && $args['background_color'] !== 'none') {
            $args['classes'][] = 'has-background';
            $args['classes'][] = 'has-'.$args['background_color'].'-background-color';
        }

        return $args;
    }

    /**
     * Adds required properties to the $args['attributes'] array.
     */
    public static function buildComponentAttributes(?array $args): ?array
    {
        if ($args === null) {
            return $args;
        }

        $args['attributes'] = $args['attributes'] ?? [];

        if (! is_array($args['attributes'])) {
            return $args;
        }

        if (! empty($args['id'])) {
            $args['attributes']['id'] = $args['id'];
        }

        return $args;
    }
}
