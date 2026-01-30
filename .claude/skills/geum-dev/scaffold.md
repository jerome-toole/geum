# Component Scaffolding

End-to-end component creation from spec to working code.

## Workflow

### 1. Read Spec

First, read `.docs/_WEBSITE-SPEC.md` and find the component definition:

```markdown
### Component Name [Block|Partial]
Description

**Fields:**
- **field_name** (type) - Description
```

### 2. Run Scaffold Script

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

Map spec fields to ACF field definitions:

| Spec Type | ACF Type | Key Pattern |
|-----------|----------|-------------|
| `text` | `text` | `field_{component}_{name}` |
| `textarea` | `textarea` | |
| `wysiwyg` | `wysiwyg` | |
| `image` | `image`, return_format: `array` | |
| `link` | `link` | |
| `true_false` | `true_false` | |
| `select` | `select` | |
| `repeater` | `repeater` | |
| `relationship` | `relationship` | |
| `post_object` | `post_object` | |

**Field Template:**
```json
{
    "key": "field_component_name_fieldname",
    "label": "Field Name",
    "name": "fieldname",
    "type": "text",
    "required": 0
}
```

**Image Field:**
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

**Repeater Field:**
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

### 5. Update Component Class

Add typed parameters matching the fields:

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

### 6. Update Template

Create simple output for each field:

```php
<?php
/**
 * ComponentName Template
 *
 * @var \Geum\Components\ComponentName $this
 */

use Geum\Helpers;
?>

<div class="<?= classes($this->classes) ?>" <?= attributes($this->attributes) ?>>
    <?php if ($this->preheading): ?>
        <span class="component-name__preheading"><?= esc_html($this->preheading); ?></span>
    <?php endif; ?>

    <?php if ($this->heading): ?>
        <h2 class="component-name__heading"><?= esc_html($this->heading); ?></h2>
    <?php endif; ?>

    <?php if ($this->body): ?>
        <div class="component-name__body"><?= $this->body; ?></div>
    <?php endif; ?>

    <?php if ($this->image): ?>
        <figure class="component-name__figure">
            <img src="<?= esc_url($this->image['url']); ?>"
                 alt="<?= esc_attr($this->image['alt']); ?>"
                 class="component-name__image">
        </figure>
    <?php endif; ?>

    <?php if ($this->link): ?>
        <a href="<?= esc_url($this->link['url']); ?>"
           class="component-name__link"
           <?= $this->link['target'] ? 'target="_blank" rel="noopener"' : ''; ?>>
            <?= esc_html($this->link['title']); ?>
        </a>
    <?php endif; ?>
</div>
```

### 7. Add Core Layout and Styles

Only add styles if you have access to a design for reference.

**Use design system utilities:**
- **Type styles**: `@apply type-{name}` (see `assets/styles/3-patterns/_type-styles.pcss`)
- **Color context**: `@apply color-context-{name}` for sections with backgrounds (auto-sets foreground)
- **Semantic colors**: `var(--color-foreground)`, `var(--color-background)` adapt to context

`components/component-name/styles.pcss`:

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

/* If component has background variants */
.component-name--dark {
    @apply color-context-slate;
}
```

## Field Output Patterns

### WYSIWYG (unescaped HTML)
```php
<?php if ($this->body): ?>
    <div class="component__body"><?= $this->body; ?></div>
<?php endif; ?>
```

### Text (escaped)
```php
<?php if ($this->heading): ?>
    <h2 class="component__heading"><?= esc_html($this->heading); ?></h2>
<?php endif; ?>
```

### Image
```php
<?php if ($this->image): ?>
    <img src="<?= esc_url($this->image['url']); ?>"
         alt="<?= esc_attr($this->image['alt']); ?>"
         width="<?= esc_attr($this->image['width']); ?>"
         height="<?= esc_attr($this->image['height']); ?>">
<?php endif; ?>
```

### Link
```php
<?php if ($this->link): ?>
    <a href="<?= esc_url($this->link['url']); ?>"
       <?= $this->link['target'] ? 'target="_blank" rel="noopener"' : ''; ?>>
        <?= esc_html($this->link['title']); ?>
    </a>
<?php endif; ?>
```

### Repeater
```php
<?php if ($this->items): ?>
    <?php foreach ($this->items as $item): ?>
        <div class="component__item">
            <?= esc_html($item['title']); ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
```

### Relationship (posts)
```php
<?php if ($this->related_posts): ?>
    <?php foreach ($this->related_posts as $post): ?>
        <?= Card::make(object: $post); ?>
    <?php endforeach; ?>
<?php endif; ?>
```

### 8. Create Example File

`components/component-name/example.php` for _dev testing suite:

```php
<?php

/**
 * ComponentName Component Examples
 */

use Geum\Components\ComponentName;

// Get a sample image (if component uses images)
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
            body: '<p>Body content goes here with <strong>formatting</strong>.</p>',
            link: [
                'url' => '#',
                'title' => 'Learn more',
                'target' => '',
            ],
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

**Example patterns by field type:**

Text/textarea:
```php
heading: 'Example Heading',
```

WYSIWYG:
```php
body: '<p>Paragraph with <a href="#">link</a> and <strong>bold</strong>.</p>',
```

Image (with sample):
```php
image: $sample_image,
```

Link:
```php
link: ['url' => '#', 'title' => 'Click here', 'target' => ''],
```

Repeater:
```php
items: [
    ['title' => 'Item 1', 'description' => 'First item'],
    ['title' => 'Item 2', 'description' => 'Second item'],
],
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
