---
name: geum-dev
description: Geum WordPress theme development workflows. Use when testing PHP/frontend changes, creating components, building from website spec, or debugging. Trigger keywords: test, debug, component, setup, spec, post type, taxonomy, route, geum.
---

# Geum Development

Workflows for the Geum WordPress theme framework.

## Available Workflows

| Workflow | Use Case |
|----------|----------|
| [Scaffold](scaffold.md) | Create component from spec (scaffold → fields → template → styles) |
| [Testing](testing.md) | Test PHP and frontend changes |
| [Components](components.md) | Component patterns and usage |
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

## Workflow Selection

When the user asks to:
- **Scaffold component** or **build component from spec** → Use [scaffold.md](scaffold.md)
- **Test changes** or **check for errors** → Use [testing.md](testing.md)
- **Component patterns** or **how to use components** → Use [components.md](components.md)
- **Create post type/taxonomy/route** → Use [setup.md](setup.md)

## Website Spec

The project spec lives at `.docs/_WEBSITE-SPEC.md`. Read this first when:
- Setting up a new project
- Adding post types, taxonomies, or routes
- Understanding the data structure
