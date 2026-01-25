# Geum

**A modern WordPress development framework** with typed PHP components, application-level routing, Tailwind v4 with custom utilities, and ACF block integration. Vite-powered build system.

*This is my personal development framework—not a polished product. Hopefully some parts are useful as reference or starting points.*

- **Components** - Co-located template, logic, styles, and scripts in portable directories
- **Router** - Editable content for archives, search, 404—deeply integrated with WordPress
- **Tailwind v4 + Utilities** - Custom layout utilities (`page-grid`, `stack-*`, `grid-auto`)
- **ACF Blocks** - Components become Gutenberg blocks with co-located field definitions
- **Vite Build** - HMR, glob imports, manifest-based cache busting
- **Dev Environment** - `/_dev` routes for component testing, style guide, and QA
- **WordPress Optimizations** - Asset handling, color system, image helpers, SVG support, menu utilities

## Key Features

### Component System
Typed PHP component classes with IDE autocomplete, validation, and transformation hooks.

```php
use Geum\Components\Accordion;
use Geum\Components\Card;

// Named arguments with full type support
echo Accordion::make(
    heading: 'FAQ',
    accordion_items: $items,
);

// From WP_Post
echo Card::make(object: $post, show_read_more: false);
```

Each component lives in `components/` with co-located files:
```
components/accordion/
├── Accordion.php      # Typed class with ::make() factory
├── template.php       # Template markup
├── styles.pcss        # Component styles (bundled)
├── scripts.js         # Component JS (bundled)
├── acf.php           # ACF block registration (optional)
└── group_*.json      # ACF field group (optional)
```

Components support:
- `validate()` - return false to skip rendering
- `transform()` - modify args before render
- `getDefaults()` - default values
- WordPress filters via `geum/component/{name}`


### Router System

Add editable block content to archives, search results, and 404 pages. Router creates WordPress pages linked to these routes—editors customize them in the block editor, and slots render dynamic content like post loops.

Integrates with WordPress' template hierarchy, query handling, and admin. No custom tables or separate admin screens.

```php
use Geum\Router;

// Blog archive with editable content around the post listing
Router::decorate('archive:post', ArchiveController::class)
    ->withContent('blog')
    ->withSlot('listing', fn() => ArchiveController::renderLoop());

// Search results with editable "no results" messaging
Router::decorate('search', SearchController::class)
    ->withContent('search')
    ->withSlot('listing', fn() => SearchController::renderResults());

// Custom URLs when you need them
Router::route('/tools/demo', fn() => DemoController::index())
    ->template('tools/demo');
```

- **Router Pages** - Auto-created WordPress pages, protected from deletion
- **Slots** - Injection points for dynamic content (`listing`, `template-content`)
- **Controllers** - Class-based or closure handlers
- **Template Resolution** - Follows WordPress hierarchy (`archive-{type}.php`, etc.)

**Route Types:**
- `archive:post` - Blog archive
- `post_type:{name}` - CPT archive
- `taxonomy:{name}` - Taxonomy archive
- `search` - Search results
- `404` - Not found


### Powerful CSS Framework w/ Tailwind v4 base

Tailwind v4 with theme-specific custom utilities for layout, spacing, and animation.

**Layout:**
- `page-grid` - 12-column grid with full/wide/prose alignment
- `grid-simple` / `grid-auto` - Flexible CSS grid utilities
- `flex-grid` - Gap-based flex layouts
- `stack-*` - Vertical rhythm spacing (e.g., `stack-24`)

**Spacing:**
- `--gap`, `--col-gap`, `--row-gap` CSS properties
- `--cols` for grid column control
- Fluid spacing utilities

Browse all utilities with live examples at `/_dev/utilities` (development only).


### ACF Blocks
Components become Gutenberg blocks by adding `acf.php`:

```php
acf_register_block_type([
    'name' => 'accordion',
    'title' => 'Accordion',
    'category' => 'theme-blocks',
    'render_callback' => function ($block) {
        $args = \Geum\Component::generateArgsFromBlock($block, get_fields());
        echo \Geum\Components\Accordion::make(...$args);
    },
]);
```

- Block field groups auto-load from component directories
- Supports anchor, className, backgroundColor from block attributes
- Preview mode for block editor

---

## Getting Started

### Requirements
- PHP 8.0+
- Node v20+ (see `.nvmrc`)

### Setup
```bash
cp .env.example .env    # Enable dev mode
npm run setup           # Install deps + build (add ACF Pro key to auth.json before running)
npm run site-setup      # Configure WP site for dev
npm run dev             # Start Vite dev server
```

Access WordPress at your normal URL (e.g., `http://geum.test`). Don't use localhost:5173 directly.

### Commands

| Command | Description |
|---------|-------------|
| `npm run dev` | Vite dev server with HMR |
| `npm run build` | Production build |
| `npm run deploy` | Full production deploy |
| `npm run scaffold` | Scaffold new component |
| `npm run pot` | Generate translation files |
| `npm run lint` | Check code (Biome) |
| `npm run fix` | Fix all code (Biome + PHP Pint) |

### Site Setup Script
`dev-scripts/site-setup.sh` configures a fresh WordPress install for development. Requires WP-CLI.

Enables debugging, sets UK locale, disables comments/pings, creates homepage, removes default content/plugins/themes, sets pretty permalinks, and activates all composer-installed plugins.

---

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
  Module.php           # Module loader
  Vite.php             # Dev server integration

Theme/                 # Custom theme functionality
  Controllers/         # Route controllers (Archive, NotFound, Search)
  Modules/             # Feature modules (auto-loaded)
    ACF/               # ACF Pro integration
    Blog/              # Blog post type & category taxonomy
    ...
  Routes/              # Route definitions (routes.php)
  Utils/               # Utilities (YearShortcode, ObjectMeta)

components/            # UI components (PHP + assets)
.docs/                 # Project documentation
  _WEBSITE-SPEC.md     # Website spec (components, data, pages)
dev-scripts/           # Build & dev tooling
assets/
  main.pcss            # Main stylesheet entry
  main.js              # Main script entry
  styles/              # CSS architecture (ITCSS)
    1-theme/           # Design tokens
    2-base/            # Base elements
    3-patterns/        # Reusable patterns
    4-utilities/       # Utility classes
public/build/          # Compiled assets
```

### Module System
Modules in `Theme/Modules/*/module.php` auto-load via `Geum\Module::init()`. Each has a `Module` class with `init()` method. Disable via `geum/modules/disabled` filter.

### Theme Configuration
`assets/theme-config.json` defines design tokens used across:
- CSS variables (`--color-darkgreen`)
- Tailwind classes (`text-darkgreen`, `bg-lightgreen`)
- Block editor color palette

### Color System
The theme includes a robust color system with color contexts, foreground calculation, and CSS variables/utilities.
Color configuration is in `assets/theme-config.json`.

**Generated CSS Variables** (per color):
```css
--color-{name}              /* hex value */
--color-{name}--hsl         /* HSL: "210 50% 40%" */
--color-{name}--h/s/l       /* individual components */
--color-{name}--foreground  /* contrasting text color */
```

**Generated Utilities**:
- `color-context-{name}` - Sets background, foreground, focus, and link colors (and other custom properties)
- `has-{name}-background-color` - WordPress block editor alias
- `foreground-from-{name}` - Sets only foreground/text color

**Usage:**
```html
<!-- Full color context (bg + text + links) -->
<section class="color-context-darkgreen">
  <p>White text on dark green, links inherit</p>
</section>

<!-- Just foreground color -->
<span class="foreground-from-brand-1">Colored text</span>
```

**Config structure** (`assets/theme-config.json`):
```json
{
  "colors": {
    "base": {
      "darkgreen": {
        "color": "#1e4545",
        "name": "Dark Green",
        "block_editor": true,
        "foreground": "var(--color-white)",
        "properties": {
          "--link--color": "var(--color-white)"
        }
      },
      "brand-1": { "namedColor": "darkgreen" }
    }
  }
}
```

### Configuration Files
- `config.json` - Framework settings (comments, images, jQuery, etc.)
- `theme.json` - WordPress FSE/block settings
- `vite.config.js` - Vite build configuration
- `postcss.config.js` - PostCSS plugins
- `tailwind.config.js` - Tailwind configuration
- `biome.json` - Biome linting/formatting
- `pint.json` - Laravel Pint PHP style

### Build System
- **Vite 7** for bundling with HMR
- **Tailwind CSS v4** via PostCSS
- **Biome** for JS/CSS linting
- Glob imports for component scripts
- Manifest-based cache busting

---

## Component Development

### Generate Component
```bash
npm run scaffold my-component
```

### Component Class Structure
```php
namespace Geum\Components;

use Geum\ComponentBase;

class MyComponent extends ComponentBase
{
    protected static string $name = 'my-component';

    public static function make(
        string $title = '',
        string $type = 'default',
        array $classes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function validate(array $args): bool
    {
        return !empty($args['title']);
    }

    protected static function transform(array $args): array
    {
        if (!empty($args['type'])) {
          $args['classes'][] = 'my-component--' . $args['type'];
        }

        return $args;
    }
}
```

### Template Access
```php
// my-component/template.php
<div class="<?= classes('my-component', $this->classes) ?>">
    <h2><?= esc_html($this->title) ?></h2>
</div>
```

---

## Development Environment (`/_dev`)

Built-in dev routes for component testing and QA. **Only available when `WP_ENVIRONMENT_TYPE=development`.**

### Routes

| URL | Description |
|-----|-------------|
| `/_dev` | Dev tools index |
| `/_dev/components` | List all components with examples |
| `/_dev/components/{name}` | View specific component examples |
| `/_dev/globals` | Theme colors, typography, spacing |
| `/_dev/utilities` | CSS utility class reference |

### Component Examples

Add `example.php` to any component to make it testable:

```php
// components/my-component/example.php
<?php
use Geum\Components\MyComponent;
?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Default</h2>
    <p class="component-example-section__description">Basic usage.</p>
    <div class="component-example-section__preview">
        <?= MyComponent::make(
            title: 'Example Title',
            content: '<p>Example content</p>',
        ); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Minimal</h2>
    <div class="component-example-section__preview">
        <?= MyComponent::make(title: 'Title Only'); ?>
    </div>
</section>
```

Use the standard `component-example-section` classes for consistent styling.

---

## AI Development (Claude Code)

The theme includes Claude Code skills in `.claude/skills/` for AI-assisted development.

### Available Skills

| Skill | Trigger | Description |
|-------|---------|-------------|
| `geum-dev` | test, debug, component, scaffold | Component development workflows |
| `website-spec` | spec, define, post type | Fill in website specification |

### geum-dev Workflows

**Scaffold** - Create components from spec:
```
scaffold component from spec → reads .docs/_WEBSITE-SPEC.md → generates files
```

**Testing** - Verify changes work:
```bash
# Quick test
: > ../../debug.log && curl -sL http://geum.test -o /dev/null && cat ../../debug.log

# Full test with Playwright
mcp__playwright__browser_navigate → snapshot → console_messages → check debug.log
```

**Components** - Patterns and usage reference for component development.

**Setup** - Create post types, taxonomies, routes from website spec.

### website-spec Workflows

Define components, data structures, and pages in `.docs/_WEBSITE-SPEC.md`:
- **Components** - Block/partial definitions with fields
- **Data Structure** - Post types, taxonomies, ACF fields
- **Pages** - Routes and templates

---

## Name

**Geum** (JEE-um) — A genus of pretty flowers whose wiry stems bear flowers for a long period from late spring through summer, commonly known as avens.

## Acknowledgements

This theme draws a lot from [Granola](https://gitlab.com/wholegrain/granola), a theme framework I worked on at Wholegrain Digital. A lot of that theme, in particular the component system, owes much to [Joshua Stopper's](https://joshstopper.com.au/) foundational work.
