# Component Development

Create and modify components in the Geum framework.

## Scaffold New Component

```bash
npm run scaffold my-component              # Class + template only
npm run scaffold my-component --styles     # Add styles.pcss
npm run scaffold my-component --block      # Add acf.php
npm run scaffold my-component --all        # All optional files
```

Options: `--styles`, `--scripts`, `--block`, `--all`

## Component Structure

Each component lives in `components/{name}/`:

```
component-name/
├── ComponentName.php    # Typed class with ::make() factory
├── template.php         # Template markup
├── acf.php              # ACF block registration (optional)
├── styles-main.css      # Styles bundled into main.css (optional)
├── scripts-main.js      # Scripts bundled into main.js (optional)
├── group_component_{name}.json  # ACF field group (optional)
└── example.php          # Usage examples (optional)
```

## After Scaffolding

The scaffold creates minimal boilerplate. Customize as needed:

### Add Typed Parameters

```php
public static function make(
    array $classes = [],
    string $title = '',
    string $content = '',
    ?array $image = null,
    ...$others
): ?static {
    return static::createFromArgs(static::mergeArgs(get_defined_vars()));
}
```

### Add Validation

```php
protected static function validate(array $args): bool
{
    return !empty($args['title']);
}
```

### Template Example

Base classes go inline in the template. Dynamic classes from `$this->classes` are added via the `classes()` helper:

```php
<div class="<?= classes('my-component', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <?php if (!empty($this->title)): ?>
        <h2 class="my-component__title"><?= esc_html($this->title); ?></h2>
    <?php endif; ?>

    <?php if (!empty($this->content)): ?>
        <div class="my-component__content"><?= $this->content; ?></div>
    <?php endif; ?>
</div>
```

- `classes('base-class', $this->classes)` - outputs base class + any dynamic classes
- `attributes($this->attributes)` - outputs data attributes, aria, etc. (not class)

## Using Components

```php
use Geum\Components\MyComponent;

// With named arguments
echo MyComponent::make(
    title: 'Hello World',
    content: '<p>Some content</p>',
    classes: ['extra-class'],
);

// With WP_Post object (if component supports it)
echo Card::make(object: $post);

// From ACF block
echo MyComponent::fromBlock($block, $fields, $content, $is_preview, $post_id);
```

## ACF Block Registration

`components/my-component/acf.php`:

```php
<?php

use Geum\Components\MyComponent;

acf_register_block_type([
    'name' => 'my-component',
    'title' => __('My Component', 'geum'),
    'category' => 'theme',
    'icon' => 'admin-generic',
    'render_callback' => fn(...$args) => print MyComponent::fromBlock(...$args),
    'supports' => [
        'anchor' => true,
        'align' => ['wide', 'full'],
    ],
]);
```

## Generate Component Class

Use the generator script:

```bash
# Generate class for existing component
node dev-scripts/generate-component-class.js my-component

# List all components
node dev-scripts/generate-component-class.js --list

# Generate for all components
node dev-scripts/generate-component-class.js --all
```

## Component Patterns

### Classes Pattern

Base classes go in the template, dynamic classes stay in `transform()`:

```php
// In template.php - base classes inline
<section class="<?= classes('my-component', 'wp-block', $this->classes) ?>" <?= attributes($this->attributes) ?>>

// In MyComponent.php transform() - only dynamic classes
protected static function transform(array $args): array
{
    $args['classes'] ??= [];

    if (!empty($args['type'])) {
        $args['classes'][] = 'my-component--' . $args['type'];
    }

    return $args;
}
```

### Nested Components

```php
protected static function transform(array $args): array
{
    if (!empty($args['heading'])) {
        $args['heading'] = [
            'heading' => $args['heading'],
            'classes' => ['parent__heading'],
        ];
    }
    return $args;
}
```

In template:
```php
<?php if (!empty($this->heading)): ?>
    <?= Heading::make(...$this->heading); ?>
<?php endif; ?>
```

### Object Mapping (WP_Post → Component)

```php
protected static function transform(array $args): array
{
    if (!empty($args['object']) && $args['object'] instanceof \WP_Post) {
        $post = $args['object'];
        $args['title'] = get_the_title($post);
        $args['url'] = get_permalink($post);
        $args['excerpt'] = get_the_excerpt($post);
    }
    return $args;
}
```

### Validation

```php
protected static function validate(array $args): bool
{
    // Skip rendering if no content
    if (empty($args['title']) && empty($args['content'])) {
        return false;
    }
    return true;
}
```

## Testing Components

After creating/modifying:

1. Clear debug log: `: > ../../debug.log`
2. Load a page using the component
3. Check for PHP errors: `cat ../../debug.log`
4. Use Playwright to verify rendering:
   - `mcp__playwright__browser_navigate` to page
   - `mcp__playwright__browser_snapshot` to check DOM
   - `mcp__playwright__browser_console_messages` for JS errors
