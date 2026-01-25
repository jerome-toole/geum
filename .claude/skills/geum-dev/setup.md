# Project Setup

Build WordPress structures from the website specification.

## Website Spec

**Always read first**: `.docs/_WEBSITE-SPEC.md`

This file defines:
- Post types and their configuration
- Taxonomies
- ACF field groups
- Components (blocks and partials)
- Routes
- Integrations

## Workflow

Work from the plan and checklist created in step 1. After each step is complete, ask for review and commit before proceeding.

1. Read `.docs/_WEBSITE-SPEC.md` and `.env` and Compare with existing files in `Theme/` and `_src/components/` to create a SITE_SETUP.md checklist.
2. Begin by setting up post types, routes and taxonomies and creating the ACF Field Groups for them.
3. Create components as defined in the spec. Use geum-dev/components.md as reference.
4. Test with debug log

## Creating Post Types

Location: `Theme/PostTypes/{Name}.php`

```php
<?php

namespace Theme\PostTypes;

class ResourceType
{
    protected const SLUG = 'resource-type';

    public static function init(): void
    {
        \add_action('init', [__CLASS__, 'registerPostType']);
        // Optional: \add_action('acf/init', [__CLASS__, 'addSettingsPage']);
    }

    public static function registerPostType(): void
    {
        if (! function_exists('register_extended_post_type')) {
            return;
        }

        \register_extended_post_type(self::SLUG, [
            'public' => true,
            'has_archive' => true,
            'hierarchical' => false,
            'show_in_rest' => true,
            'menu_position' => 25,
            'menu_icon' => 'dashicons-admin-generic',
            'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
            'taxonomies' => [],
            'admin_cols' => [
                'title' => ['title' => 'Title'],
                'updated' => [
                    'title' => 'Updated',
                    'post_field' => 'post_modified',
                    'date_format' => 'Y/m/d',
                ],
            ],
        ], [
            'singular' => __('Resource Type', 'geum'),
            'plural' => __('Resource Types', 'geum'),
            'slug' => self::SLUG,
        ]);
    }
}
```

Register in `functions.php`:
```php
\Theme\PostTypes\ResourceType::init();
```

## Creating Taxonomies

Location: `Theme/Taxonomies/{Name}.php`

```php
<?php

namespace Theme\Taxonomies;

class Topic
{
    protected const SLUG = 'topic';

    public static function init(): void
    {
        \add_action('init', [__CLASS__, 'registerTaxonomy']);
    }

    public static function registerTaxonomy(): void
    {
        if (! function_exists('register_extended_taxonomy')) {
            return;
        }

        \register_extended_taxonomy(
            self::SLUG,
            ['post', 'resource'],  // Post types
            [
                'hierarchical' => true,
                'show_admin_column' => true,
                'show_in_rest' => true,
                'meta_box' => 'simple',
            ],
            [
                'singular' => __('Topic', 'geum'),
                'plural' => __('Topics', 'geum'),
                'slug' => self::SLUG,
            ]
        );
    }
}
```

Register in `functions.php`:
```php
\Theme\Taxonomies\Topic::init();
```

## Creating Components

See [components.md](components.md) for full details.

Quick reference for spec-defined components:

1. Create directory: `components/{name}/`
2. Create class: `{Name}.php` with `::make()` factory
3. Create template: `template.php`
4. If block: Create `acf.php` with `acf_register_block_type()`

## Creating Routes

Location: `Theme/Routes/routes.php`

```php
use Geum\Router;
use Theme\Controllers\ResourcesController;

// Decorate WordPress archive
Router::decorate('post_type:resources', ResourcesController::class)
    ->withContent('resources-listing')
    ->withSlot('listing', fn() => ResourcesController::renderLoop());

// Custom owned route
Router::route('/tools/calculator', fn() => CalculatorController::index())
    ->noContent()
    ->template('tools/calculator');
```

## ACF Field Groups

For non-component fields (options pages, post type fields):

1. Create in WP Admin > Custom Fields
2. Export JSON to `acf-json/`
3. Document in `.docs/_WEBSITE-SPEC.md`

For component fields:

1. Create field group with location = Block
2. JSON auto-saves to component directory
3. Reference in component's `acf.php`

## Checklist from Spec

When reading the spec, check for:

| Spec Section | Create |
|--------------|--------|
| Post Types | `Theme/PostTypes/{Name}.php` |
| Taxonomies | `Theme/Taxonomies/{Name}.php` |
| Components [Block] | `components/{name}/` with `acf.php` |
| Components [Partial] | `components/{name}/` without `acf.php` |
| Theme Options | ACF Options page fields |
| Routes/Archives | `Theme/Routes/routes.php` entries |

## Naming Conventions

| Type | Class Name | Slug | File |
|------|------------|------|------|
| Post Type | `ResourceType` | `resource-type` | `ResourceType.php` |
| Taxonomy | `ResourceCategory` | `resource-category` | `ResourceCategory.php` |
| Component | `PageHeader` | `page-header` | `PageHeader.php` |

## Verify Setup

After creating structures:

```bash
# Clear log
: > ../../debug.log

# Load site
curl -sL {{APP_URL}} -o /dev/null -w "%{http_code}\n"

# Check for errors
cat ../../debug.log

# Verify post type registered
curl -s "{{APP_URL}}/wp-json/wp/v2/types" | grep "resource-type"
```

## Dependencies

Uses Extended CPTs library. `johnbillion/extended-cpts`
