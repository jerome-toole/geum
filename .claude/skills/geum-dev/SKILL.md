---
name: geum-dev
description: Geum WordPress theme development workflows. Use when testing PHP/frontend changes, creating components, building from website spec, or debugging. Trigger keywords: test, debug, component, setup, spec, post type, taxonomy, route, geum.
---

# Geum Development

Workflows for the Geum WordPress theme framework.

## Available Workflows

| Workflow | Use Case |
|----------|----------|
| [Components](components.md) | Scaffold, build, and use components |
| [Patterns](patterns.md) | CSS architecture, colors, spacing, layout |
| [Testing](testing.md) | Test PHP and frontend changes |
| [Setup](setup.md) | Build from website spec (post types, taxonomies, routes) |

## Quick Reference

### Project Structure
- `Geum/` - Core framework classes
- `Theme/` - Custom theme functionality
- `components/` - Component source files
- `public/` - Built assets

### Key Commands
```bash
npm run dev      # Start Vite dev server
npm run build    # Production build
npm run fix      # Fix linting issues
```

### Debug Log Location
```
../../debug.log  # Relative to theme root
```

### Site URL
Check the local WordPress URL in .env

### Browser Testing (Chrome DevTools MCP)
```
mcp__chrome-devtools__navigate_page      # Load page
mcp__chrome-devtools__take_snapshot      # Inspect DOM
mcp__chrome-devtools__list_console_messages  # Check JS errors
mcp__chrome-devtools__take_screenshot    # Visual check
```

## Workflow Selection

When the user asks to:
- **Scaffold component** or **build component from spec** → Use [components.md](components.md)
- **CSS patterns** or **colors/spacing/layout** → Use [patterns.md](patterns.md)
- **Test changes** or **check for errors** → Use [testing.md](testing.md)
- **Create post type/taxonomy/route** → Use [setup.md](setup.md)

## Styling Systems

### Type Styles
Defined in `assets/styles/3-patterns/_type-styles.pcss`. Predefined typography utilities bundle font-family, size, weight, line-height, and letter-spacing. **Always use these instead of raw Tailwind typography utilities.**

- Available as `@apply type-{name}` in CSS or class names
- Semantic names: `type-hero`, `type-h1`-`type-h6`, `type-base`, `type-small`, `type-meta`, `type-caption`
- WordPress block styles also available: `.is-style-type-{name}`
- Uses `rfs()` for responsive font sizing

### Color System
Colors defined in `assets/theme-config.json`, processed by `build-scripts/postcss-color-system.js`.

**CSS Custom Properties:**
- `--color-{name}` - Raw color value
- `--color-{name}--foreground` - Appropriate text color for that background
- `--color-foreground` / `--color-background` - Contextual colors (change based on context)

**Color Context Utilities:**
- `color-context-{name}` - Sets background AND automatically sets foreground/link colors
- `has-{name}-background-color` - WordPress alias (same as color-context)
- `foreground-from-{name}` - Sets only foreground color from a color's contrast pair

**Usage Pattern:**
```css
/* DO: Use color context for sections with background colors */
.my-section {
  @apply color-context-slate;
}

/* DO: Use semantic properties within a context */
.my-element {
  color: var(--color-foreground);
  background-color: var(--color-background);
}

/* DON'T: Set background without considering foreground */
.my-section {
  background-color: var(--color-slate); /* Missing foreground! */
}
```

When adding colored sections, prefer `color-context-{name}` which handles:
- Background color
- Foreground/text color
- Link colors
- Focus states

## Website Spec

The project spec lives at `.docs/_WEBSITE-SPEC.md`. Read this first when:
- Setting up a new project
- Adding post types, taxonomies, or routes
- Understanding the data structure
