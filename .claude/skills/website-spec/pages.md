# Pages and Routes Definition

Define pages, routes, and templates in the website spec.

## Workflow

### Standard Pages

1. Ask user for:
   - Page name and URL
   - Template type
   - Custom fields (if any)
   - Components used

2. Add to spec under `## Pages and Routes`:

```markdown
### Page Name (/url/)
- Template: Default | Custom
- Fields:
  - **field_name** (type) - Description
```

### Archive/Listing Pages

```markdown
### Post Type Archive (/url/)
- Template: Listing
- Route: decorate:post_type:{slug}
- Renders: {Listing Component}
```

### Custom Routes

For routes not tied to WordPress pages:

```markdown
### Route Name (/custom/url/)
- Template: Custom
- Route: route
- Controller: {ControllerClass}
```

### Single Post Templates

```markdown
### {Post Type} Single
- Template: {Template Name}
- Displays: {what it shows}
- Fields:
  - **field_name** (type) - Description
```

## Route Types

| Type | Use For |
|------|---------|
| (none) | Standard WP pages |
| `decorate:post_type:{slug}` | Post type archives |
| `decorate:taxonomy:{slug}` | Taxonomy archives |
| `decorate:search` | Search results |
| `decorate:404` | 404 page |
| `route` | Custom owned routes |

## Template Types

| Template | Use For |
|----------|---------|
| Default | Standard pages with blocks |
| Listing | Archive/listing pages |
| Single | Post type single pages |
| Custom | Special purpose templates |

## Prompt Template

```
Define **{Page/Route}**:
- URL?
- What does it display?
- Custom fields needed?
- Which components does it use?
```
