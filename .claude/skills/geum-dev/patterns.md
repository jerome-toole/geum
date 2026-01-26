# Development Patterns

CSS architecture, design tokens, and layout patterns.

## CSS Approach

Use both **Tailwind** and **BEM**:
- **Tailwind** - Inline classes for layout and type (`flex`, `gap-4`, `type-h2`, our custom utilities)
- **BEM** - Component styles for custom/complex work (`.card__title`, `.hero--large`)

```php
<div class="card flex flex-col gap-4">
    <h2 class="card__title type-h2"><?= $title ?></h2>
    <div class="card__body prose"><?= $content ?></div>
</div>
```

## Colors

Colors defined in `assets/theme-config.json`. Use CSS custom properties:

```pcss
.component {
    color: var(--color-neutral);
    background: var(--color-neutral);
}
```

**Generated per color:**
- `--color-{name}` - hex
- `--color-{name}--hsl` - HSL format
- `--color-{name}--foreground` - contrasting text

### Color Context Utilities

Set background + foreground + link colors:

```php
<section class="color-context-neutral">
    <!-- Background: neutral, text: white, links: white -->
</section>
```

Sets: `--color-background`, `--color-foreground`, `background-color`, `color`, `--link--color`, `--focus--color`

**Also available:**
- `has-{color}-background-color` - WordPress block editor alias
- `foreground-from-{color}` - text color only (no background)

## Spacing

Use `space()` function (converts px to rem):

```pcss
.component {
    padding: space(16);           /* 1rem */
    margin-block: space(32);      /* 2rem */
    gap: space(8);                /* 0.5rem */
}
```

**Responsive with `spaceFluid()`:**
```pcss
.component {
    padding: spaceFluid(16, 32);  /* Fluid from 1rem to 2rem */
}
```

**Layout variables:**
- `var(--space--layout)` - Block spacing (32px → 64px responsive)
- `var(--space--container-padding)` - Side padding (16px → 30px responsive)
- `var(--space--base)` - Text element spacing (16px)

## Page Grid

WordPress block content layout:

```php
<div class="page-grid">
    <!-- Children span 12-col "wide" area by default -->
    <div class="alignfull"><!-- Full viewport width --></div>
    <div class="alignwide"><!-- Content width --></div>
</div>
```

Grid: `[full-start] gutter [wide-start] 12-cols [wide-end] gutter [full-end]`

**Alignment classes:**
| Class | Grid Column |
|-------|-------------|
| `alignfull` | `full` (full viewport) |
| `alignwide` | `wide` (content width) |
| `alignleft` | `2 / 8` |
| `alignright` | `8 / 14` |
| `alignprose` | `wide` + max-width 65ch |

## Typography

**Type utilities** (use with `@apply`):

```pcss
.component__heading {
    @apply type-h2;
}

.component__preheading {
    @apply type-meta;
}
```

| Utility | Size |
|---------|------|
| `type-hero` | 36px → 85px |
| `type-h1` | 28px → 55px |
| `type-h2` | 26px → 44px |
| `type-h3` | 23px → 35px |
| `type-h4` | 20px → 28px |
| `type-h5` | 18px → 22px |
| `type-h6` | base size |
| `type-base` | body text |
| `type-small` | 14px |
| `type-meta` | base size, tight line-height |

**Responsive font with `rfs()`:**
```pcss
.custom-heading {
    font-size: rfs(24, 36);  /* 24px at min, 36px at max viewport */
}
```

**Font families:**
- `var(--type--1--font-family)` - Body (system-ui)
- `var(--type--2--font-family)` - Headings (Georgia)

## Other Functions

| Function | Example | Output |
|----------|---------|--------|
| `rem(px)` | `rem(24)` | `1.5rem` |
| `em(px)` | `em(24)` | `1.5em` |
| `strip-unit(val)` | `strip-unit(16px)` | `16` |
| `transition(props...)` | `transition(opacity, transform)` | transition string |

## File Structure

```
assets/styles/
├── 1-theme/         # Variables: space, type, colors, widths
├── 2-base/          # Normalize, fonts, base elements
├── 3-patterns/      # Reusable: page-grid, buttons, forms, type-styles
└── 4-utilities/     # Helpers: alignment, screen-reader, etc.
```
