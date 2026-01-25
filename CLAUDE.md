# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Modern WordPress development framework** with typed PHP components, application-level routing, Tailwind v4, and ACF block integration. Vite-powered build system.


## Development Commands

### Setup
```bash
npm run setup        # npm install && composer install && npm run build
```

### Development
```bash
npm run dev         # Vite dev server with HMR
npm run build       # Production build
npm run preview     # Preview production build
npm run deploy      # npm install && composer install --no-dev && npm run build && npm run pot
npm run pot         # Generate translation files
npm run scaffold    # Scaffold new component
```

### Code Quality
```bash
npm run lint        # Check with Biome
npm run lint:fix    # Fix with Biome
npm run format      # Format with Biome
npm run fix         # Fix everything (Biome + PHP Pint)
npm run fix:php     # Fix PHP with Laravel Pint
```

### Environment
- Node v20+ (see `.nvmrc`)
- PHP 8.0+

### Dev Workflow
1. `npm run dev`
2. Access WP at normal URL (e.g., `http://geum.test`)
3. HMR auto-detects; don't access localhost:5173 directly


## Architecture

### Directory Structure
```
Geum/                  # Core framework
  WordPress/           # WP integrations (Admin, Cleanup, Enqueue, Gutenberg, etc.)
  Router/              # Routing system classes
  Component.php        # Component discovery & ACF blocks
  ComponentBase.php    # Base class for typed components
  Config.php           # Framework config loader
  Helpers.php          # Global helper functions
  Image.php            # Image handling
  Module.php           # Module loader
  SVG.php              # SVG utilities
  Vite.php             # Dev server integration

Theme/                 # Custom theme functionality
  Controllers/         # Route controllers (Archive, NotFound, Search)
  Modules/             # Feature modules (auto-loaded)
    ACF/               # ACF Pro integration
    Analytics/         # Analytics setup
    Blog/              # Blog post type & category taxonomy
    Core/              # Core theme features (Menus, Sidebars, Preloads)
    Events/            # Events post type & location taxonomy
    GravityForms/      # Gravity Forms integration
    Pages/             # Pages post type
    Yoast/             # Yoast SEO integration
  Routes/              # Application routes (routes.php)
  Utils/               # Utilities (YearShortcode, ObjectMeta)

components/            # UI components (PHP + assets)
assets/                # Build source files
  styles/              # CSS architecture (1-theme, 2-base, 3-patterns, 4-utilities)
  scripts/             # JS helpers
  static/              # Static assets (copied to public/)
public/                # Built assets (build/manifest.json)
acf-json/              # ACF field groups
```

### Module System
Modules in `Theme/Modules/*/module.php` auto-load via `Geum\Module::init()`. Each module has a `Module` class with `init()` method. Disable via `geum/modules/disabled` filter.

### Component System
Components in `components/` with structure:
- `ComponentName.php` - Typed class with `::make()` factory
- `template.php` - Template markup
- `styles.pcss` - Bundled into main.css
- `scripts.js` - Bundled into main.js
- `acf.php` - ACF block config (optional)
- `example.php` - Dev preview examples (optional)
- `group_component_{name}.json` - ACF field group (optional)

Namespaced under `Geum\Components`.

**Usage:**
```php
use Geum\Components\Accordion;
use Geum\Components\Card;

echo Accordion::make(
    heading: 'FAQ',
    accordion_items: $items,
);

echo Card::make(object: $post, show_read_more: false);

// From ACF block
echo Accordion::fromBlock($block, $fields, $content, $is_preview, $post_id);
```

**Class Structure:**
```php
namespace Geum\Components;

use Geum\ComponentBase;

class ComponentName extends ComponentBase
{
    protected static string $name = 'component-name';

    public static function make(
        array $classes = [],
        string $title = '',
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    // Optional: return false to skip rendering
    protected static function validate(array $args): bool
    {
        return !empty($args['title']);
    }

    // Transform args before rendering
    protected static function transform(array $args): array
    {
        $args['classes'] = array_merge(['component-name'], $args['classes'] ?? []);
        return $args;
    }
}
```

**Template Access:**
```php
// In template.php - use $this->property
<div class="<?= classes($this->classes) ?>" <?= attributes($this->attributes) ?>>
    <?= Heading::make(...$this->content['heading']); ?>
</div>
```

**External Filtering:**
```php
add_filter('geum/component/accordion', function($args) {
    $args['classes'][] = 'custom-class';
    return $args;
});
```

**Generate Component Classes:**
```bash
node dev-scripts/generate-component-class.js accordion     # Single
node dev-scripts/generate-component-class.js --all        # All
node dev-scripts/generate-component-class.js --list       # List
```

### Router System
Application-level routing for owned routes and WordPress archive decoration.

**Define in `Theme/Routes/routes.php`:**
```php
use Geum\Router;
use Theme\Controllers\ArchiveController;

// Decorate WordPress archives
Router::decorate('archive:post', ArchiveController::class)
    ->withContent('blog')
    ->withSlot('listing', fn() => ArchiveController::renderLoop());

// 404 page
Router::decorate('404', NotFoundController::class)
    ->withContent('404')
    ->withSlot('template-content', fn() => NotFoundController::renderContent());

// Custom post type archive
Router::decorate('post_type:event', ArchiveController::class)
    ->withContent('events')
    ->withSlot('listing', fn() => ArchiveController::renderLoop());
```

**Route Types:**
- `archive:post` - Blog archive
- `post_type:{name}` - CPT archive
- `taxonomy:{name}` - Taxonomy archive
- `search` - Search results
- `404` - Not found

### Build System
- **Vite 7** with laravel-vite-plugin
- **Tailwind CSS v4** via PostCSS
- **Biome** for JS/CSS linting
- Custom glob import plugins for CSS & JS

**Entry Points:**
- `assets/main.js` → `public/build/main.js`
- `assets/main.pcss` → `public/build/main-styles.css`
- `assets/editor-scripts.js` → `public/build/editor-scripts.js`
- `assets/editor-styles.pcss` → `public/build/editor-styles.css`
- `assets/admin-scripts.js` → `public/build/admin-scripts.js`

**Component Assets:**
- `styles.pcss` / `scripts.js` - Bundled via glob into main entry points
- Other named files (e.g., `button.js`) - Built as standalone entries

### Configuration Files
- `config.json` - Framework settings
- `theme.json` - WordPress FSE/block settings
- `vite.config.js` - Vite configuration
- `postcss.config.js` - PostCSS plugins
- `tailwind.config.js` - Tailwind configuration
- `biome.json` - Biome linting
- `pint.json` - Laravel Pint PHP style

### PHP Architecture
- PSR-4 autoloading: `Geum\` → `Geum/`, `Theme\` → `Theme/`
- Components classmap in `composer.json`
- Laravel Pint for formatting (PSR-12)


## CSS Architecture

Organized in `assets/styles/` following ITCSS:
- `1-theme/` - Design tokens, variables
- `2-base/` - Normalize, base elements
- `3-patterns/` - Reusable patterns
- `4-utilities/` - Utility classes

**Tailwind v4** with custom utilities. Import: `@import 'tailwindcss';`

### Color System
`dev-scripts/postcss-color-system.js` generates from `assets/theme-config.json`:

**CSS Variables** (per color):
- `--color-{name}` - hex value
- `--color-{name}--hsl` - HSL format
- `--color-{name}--foreground` - contrasting text

**Utilities**:
- `color-context-{name}` - background + foreground + focus + links
- `has-{name}-background-color` - WP block editor alias
- `foreground-from-{name}` - text color only

```html
<section class="color-context-darkgreen">...</section>
<span class="foreground-from-brand-1">...</span>
```


## JavaScript Architecture

- Entry points: `main.js`, `editor-scripts.js`, `admin-scripts.js`
- Glob imports: `import '../components/*/scripts.js'`
- Modern ES modules (Vite handles transpilation)


## Theme Initialization Flow

1. `functions.php` loads Composer autoloader
2. `Geum\Config::init()` loads `config.json`
3. `Geum\Component::init()` discovers components
4. `Geum\WordPress\*::init()` classes initialize
5. `Geum\Router::init()` loads routes
6. `Geum\Module::init()` loads Theme modules
7. Theme utilities initialize


## WordPress Integration

- ACF Pro for custom fields (`acf-json/`)
- Extended CPTs for post type registration
- Block editor customization via `Geum\WordPress\Gutenberg`
- Plugin deps: Query Monitor, Safe SVG, Yoast SEO (via Composer)


## Testing & Quality

- PHP: Laravel Pint (PSR-12)
- JS/CSS: Biome
- Run `npm run lint` to check
- Run `npm run fix` to fix
