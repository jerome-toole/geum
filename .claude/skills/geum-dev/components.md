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

```
components/component-name/
├── ComponentName.php                       # Typed class with ::make() factory
├── template.php                            # Template markup
├── acf.php                                 # ACF block registration (optional)
├── styles.pcss                             # Bundled into main.css (optional)
├── scripts.js                              # Bundled into main.js (optional)
├── group_component_component_name.json     # ACF field group (optional)
└── example.php                             # Dev preview examples (optional)
```

## Scaffold from Spec Workflow

### 1. Read Spec

Find component definition in `.docs/_WEBSITE-SPEC.md`:

```markdown
### Component Name [Block|Partial]
Description

**Fields:**
- **field_name** (type) - Description
```

### 2. Run Scaffold

```bash
# For blocks (has ACF fields)
node dev-scripts/scaffold-component.js component-name --styles --block

# For partials (no ACF, data from context)
node dev-scripts/scaffold-component.js component-name --styles
```

### 3. Create ACF Field Group

For blocks, create `components/component-name/group_component_component_name.json`:

```json
{
    "key": "group_component_component_name",
    "title": "Component Name",
    "fields": [],
    "location": [[{
        "param": "block",
        "operator": "==",
        "value": "acf/component-name"
    }]]
}
```

### 4. Add Fields to JSON

Map spec fields to ACF:

| Spec Type | ACF Type | Notes |
|-----------|----------|-------|
| `text` | `text` | |
| `textarea` | `textarea` | |
| `wysiwyg` | `wysiwyg` | |
| `image` | `image` | return_format: `array` |
| `link` | `link` | |
| `true_false` | `true_false` | |
| `select` | `select` | |
| `repeater` | `repeater` | |
| `relationship` | `relationship` | |
| `post_object` | `post_object` | |

**Field template:**
```json
{
    "key": "field_component_name_fieldname",
    "label": "Field Name",
    "name": "fieldname",
    "type": "text",
    "required": 0
}
```

**Image field:**
```json
{
    "key": "field_component_name_image",
    "label": "Image",
    "name": "image",
    "type": "image",
    "return_format": "array",
    "preview_size": "medium"
}
```

**Repeater field:**
```json
{
    "key": "field_component_name_items",
    "label": "Items",
    "name": "items",
    "type": "repeater",
    "layout": "block",
    "sub_fields": []
}
```

## Component Class

### Typed Parameters

```php
public static function make(
    array $classes = [],
    string $preheading = '',
    string $heading = '',
    string $body = '',
    ?array $link = null,
    ?array $image = null,
    ...$others
): ?static {
    return static::createFromArgs(static::mergeArgs(get_defined_vars()));
}
```

### Validation

```php
protected static function validate(array $args): bool
{
    return !empty($args['title']);
}
```

### Transform

```php
protected static function transform(array $args): array
{
    $args['classes'] ??= [];

    if (!empty($args['type'])) {
        $args['classes'][] = 'my-component--' . $args['type'];
    }

    return $args;
}
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

## Template Patterns

Base classes inline, dynamic via `classes()`:

```php
<?php
/**
 * ComponentName Template
 *
 * @var \Geum\Components\ComponentName $this
 */

use Geum\Helpers;
?>

<div class="<?= classes('component-name', $this->classes) ?>" <?= Helpers::buildAttributes($this->attributes); ?>>
    ...
</div>
```

### Field Output Patterns

**Text (escaped):**
```php
<?php if ($this->heading): ?>
    <h2 class="component__heading"><?= esc_html($this->heading); ?></h2>
<?php endif; ?>
```

**WYSIWYG (unescaped HTML):**
```php
<?php if ($this->body): ?>
    <div class="component__body"><?= $this->body; ?></div>
<?php endif; ?>
```

**Image:**
```php
<?php if ($this->image): ?>
    <img src="<?= esc_url($this->image['url']); ?>"
         alt="<?= esc_attr($this->image['alt']); ?>"
         width="<?= esc_attr($this->image['width']); ?>"
         height="<?= esc_attr($this->image['height']); ?>">
<?php endif; ?>
```

**Link:**
```php
<?php if ($this->link): ?>
    <a href="<?= esc_url($this->link['url']); ?>"
       <?= $this->link['target'] ? 'target="_blank" rel="noopener"' : ''; ?>>
        <?= esc_html($this->link['title']); ?>
    </a>
<?php endif; ?>
```

**Repeater:**
```php
<?php if ($this->items): ?>
    <?php foreach ($this->items as $item): ?>
        <div class="component__item">
            <?= esc_html($item['title']); ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
```

**Relationship (posts):**
```php
<?php if ($this->related_posts): ?>
    <?php foreach ($this->related_posts as $post): ?>
        <?= Card::make(object: $post); ?>
    <?php endforeach; ?>
<?php endif; ?>
```

## Component Styles

```pcss
.component-name {
    display: grid;
    gap: space(8);

    .component-name__preheading {
        @apply type-meta;
    }

    .component-name__heading {
        @apply type-h2;
    }
}
```

## Example File

Create `components/component-name/example.php` for dev testing:

```php
<?php

/**
 * ComponentName Component Examples
 */

use Geum\Components\ComponentName;

// Get sample image
$attachments = get_posts([
    'post_type' => 'attachment',
    'post_mime_type' => 'image',
    'posts_per_page' => 1,
    'post_status' => 'inherit',
]);
$sample_image = !empty($attachments) ? acf_get_attachment($attachments[0]->ID) : null;

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Component Name - Default</h2>
    <p class="component-example-section__description">Basic usage with all fields.</p>
    <div class="component-example-section__preview">
        <?= ComponentName::make(
            preheading: 'Preheading Text',
            heading: 'Main Heading',
            body: '<p>Body content with <strong>formatting</strong>.</p>',
            link: ['url' => '#', 'title' => 'Learn more', 'target' => ''],
            image: $sample_image,
        ); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Component Name - Minimal</h2>
    <p class="component-example-section__description">Only required fields.</p>
    <div class="component-example-section__preview">
        <?= ComponentName::make(
            heading: 'Heading Only',
        ); ?>
    </div>
</section>
```

**Example data by field type:**
- Text: `heading: 'Example Heading',`
- WYSIWYG: `body: '<p>Paragraph with <a href="#">link</a>.</p>',`
- Image: `image: $sample_image,`
- Link: `link: ['url' => '#', 'title' => 'Click here', 'target' => ''],`
- Repeater: `items: [['title' => 'Item 1'], ['title' => 'Item 2']],`

## Using Components

```php
use Geum\Components\MyComponent;

// With named arguments
echo MyComponent::make(
    title: 'Hello World',
    content: '<p>Some content</p>',
    classes: ['extra-class'],
);

// With WP_Post object
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

## Verify

After scaffolding:

```bash
# Clear log
: > ../../debug.log

# Build assets
npm run build

# Load page with component
curl -sL http://geum.test -o /dev/null

# Check for errors
cat ../../debug.log
```

## Component Patterns

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

## Styling Components

### Type Styles

Use predefined type utilities instead of raw font properties:

```pcss
.my-component__heading {
    @apply type-h2;  /* NOT: font-size: 2rem; font-weight: 700; */
}

.my-component__meta {
    @apply type-meta;
}
```

See `assets/styles/3-patterns/_type-styles.pcss` for definitions.

### Color Context

For sections with background colors, use `color-context-{name}` which sets background AND appropriate foreground/link colors:

```pcss
.my-component--dark {
    @apply color-context-slate;  /* Sets bg, text color, link colors */
}
```

Within a color context, use semantic properties:

```pcss
.my-component__text {
    color: var(--color-foreground);  /* Adapts to context */
}
```

Colors defined in `assets/theme-config.json`, utilities generated by `build-scripts/postcss-color-system.js`.

## Testing Components

After creating/modifying:

1. Clear debug log: `: > ../../debug.log`
2. Load a page using the component
3. Check for PHP errors: `cat ../../debug.log`
4. Use Chrome DevTools MCP to verify rendering:
   - `mcp__chrome-devtools__navigate_page` to page
   - `mcp__chrome-devtools__take_snapshot` to check DOM
   - `mcp__chrome-devtools__list_console_messages` for JS errors
   - `mcp__chrome-devtools__take_screenshot` to visually verify
