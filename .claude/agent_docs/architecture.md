# Architecture

## Theme Initialization Flow

```
functions.php
  1. Composer autoloader
  2. Geum\Config::init()        → loads config.json
  3. Geum\Component::init()     → discovers components in components/
  4. Geum\WordPress\*::init()   → WP hooks, enqueue, etc.
  5. Geum\Router::init()        → loads Theme/Routes/routes.php
  6. Geum\Module::init()        → loads Theme/Modules/*/module.php
  7. Theme utilities initialize
```

## PHP Namespaces & Autoloading

PSR-4 via Composer:
- `Geum\` → `Geum/`
- `Theme\` → `Theme/`
- `Geum\Components\` → `components/` (classmap — not PSR-4)

## Module System

**Convention:** `Theme/Modules/{Name}/module.php` with a `Module` class containing a static `init()` method.

Auto-loaded by `Geum\Module::init()`. To disable a module:
```php
add_filter('geum/modules/disabled', fn($disabled) => [...$disabled, 'ModuleName']);
```

## Component System

**Directory:** `components/{ComponentName}/`

| File | Purpose |
|------|---------|
| `ComponentName.php` | Typed class with `::make()` factory |
| `template.php` | Template markup (uses `$this->property`) |
| `styles.pcss` | Bundled into main CSS |
| `scripts.js` | Bundled into main JS |
| `acf.php` | ACF block config (optional) |
| `example.php` | Dev preview examples (optional) |
| `group_component_{name}.json` | ACF field group (optional) |

**Class structure:**
```php
namespace Geum\Components;
use Geum\ComponentBase;

class ComponentName extends ComponentBase
{
    protected static string $name = 'component-name';

    public static function make(array $classes = [], string $title = ''): ?static
    {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    // Optional: return false to skip rendering
    protected static function validate(array $args): bool { ... }

    // Optional: transform args before rendering
    protected static function transform(array $args): array { ... }
}
```

**External filter hook:**
```php
add_filter('geum/component/accordion', fn($args) => $args);
```

**Generate component class from existing directory:**
```bash
node dev-scripts/generate-component-class.js accordion  # Single
node dev-scripts/generate-component-class.js --all     # All
```

## Router System

`Theme/Routes/routes.php` — application-level routing for owned routes and WordPress archive decoration.
See `Theme/Router.php` for implementation.

## WordPress Integration

- **ACF Pro** — custom fields; field groups sync via `acf-json/`
- **Extended CPTs** — post type and taxonomy registration helpers
- **Plugin deps** (Composer): ACF Pro, Query Monitor, Safe SVG, Yoast SEO
