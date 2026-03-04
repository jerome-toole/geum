# CLAUDE.md

Geum is a WordPress development framework built on top of the WordPress ecosystem, designed to provide a modern development experience while adhering to WordPress best practices.

Key features include:
- **Components**: Typed PHP classes (`Geum\Components\*`) with `::make()` factories, optional `validate()` and `transform()` hooks. Auto-discovered from `components/`. Editor integration via ACF blocks (`acf.php` per component).
- **Module System**: Encapsulated theme features in `Theme/Modules/*/module.php`, each with a static `init()`. Auto-loaded; disable via `geum/modules/disabled` filter.
- **Router**: `Theme/Routes/routes.php` handles owned routes and WordPress archive decoration via `Geum\Router`.
- **ACF Pro**: Custom fields, option pages, and editor blocks. Field groups managed as local JSON in `acf-json/`. Blocks render via `ComponentName::fromBlock()`.
- **CSS Architecture**: Tailwind + BEM hybrid. Tailwind for layout utilities in templates (`flex`, `gap-4`); per-component `styles.pcss` (BEM) for component styles. Custom type, color, and spacing systems — never use raw Tailwind typography or color classes in components.
- **Build System**: Vite with per-component entry points. Component `styles.pcss` and `scripts.js` are auto-bundled into the main CSS/JS.


### Dev Workflow
1. `npm run dev` — HMR auto-detects;
2. `npm run lint` / `npm run fix` — JS/CSS (Biome)
3. `./vendor/bin/pint` — PHP formatting (PSR-12)
4. Access WP at `APP_URL` from `.env`


### Module System
Modules in `Theme/Modules/*/module.php` auto-load via `Geum\Module::init()`.
Each has a static `init()` method. Disable via `geum/modules/disabled` filter.


### MCPs to use:
- **context7** — look up library/framework docs before implementing with any SDK or package
- **chrome-devtools** — browser testing; navigate, snapshot DOM, check console/network


### Styling Rules

**Typography**: Use `type-{name}` utilities — `@apply type-h2`, `type-meta`, etc. Never raw Tailwind font/size properties. Defined in `assets/styles/3-patterns/_type-styles.pcss`. Semantic names: `type-hero`, `type-h1`–`type-h6`, `type-base`, `type-meta`.

**Colors**: Defined in `assets/theme-config.json`, processed at build time into CSS custom properties. Each color generates:
- `--color-{name}` — raw hex
- `--color-{name}--foreground` — contrasting text color
- `color-context-{name}` utility — sets background AND `--color-background`, `--color-foreground`, link/focus colors together

Always use `color-context-{name}` for sections with a background — never set `background-color` alone. Within a color context use `var(--color-foreground)` / `var(--color-background)` for text and backgrounds.

**Spacing**: Use `space(px)` (converts px → rem). Responsive: `spaceFluid(min, max)`. Layout tokens: `var(--space-layout)`, `var(--container-padding)`.

**Patterns vs utilities**: Use Tailwind utilities (`flex`, `gap-4`, `hidden`) for layout directly in templates. Use `@apply` with custom patterns (`type-h2`, `color-context-name`) inside component `.pcss` files — not as inline template classes.


### Testing
- **PHP errors**: `cat ../../debug.log` after page load
- **WP Login**: If you need to log in but don't have credentials, create a user via WP CLI: `wp user create testadmin --role=administrator --user_pass=strongpassword`
- **WP data**: WP CLI — run with `--path=../../../../` (e.g. `wp post-type list`)
- **Rendering/UI**: Chrome DevTools MCP — navigate, snapshot DOM, check console. Prefer DOM over screenshots unless visual testing is needed.
- **Visual changes**: Take a screenshot and ask the user to confirm before marking done.
- Get `APP_URL` from `.env`: `APP_URL=$(grep '^APP_URL' .env | cut -d= -f2)`
- Component previews: `$APP_URL/_dev/`


### Reference

| Doc | When to read |
|-----|-------------|
| [architecture.md](.claude/agent_docs/architecture.md) | Theme init flow, module system internals, WP integration |
| [css.md](.claude/agent_docs/css.md) | CSS architecture, color system, spacing, type utilities, page grid |

## Skills
- `.claude/skills/geum-dev/` — Dev workflows (components, CSS, testing, setup, build)
- `.claude/skills/website-spec/` — Used for writing a WEBSITE_SPEC.md defining pages, routes, templates, and components for a project.
