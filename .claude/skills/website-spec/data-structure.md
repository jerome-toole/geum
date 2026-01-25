# Data Structure Definition

Define post types, taxonomies, and ACF fields in the website spec.

## Workflow

### Post Types

1. Ask user for:
   - Post type name/slug
   - Purpose/description
   - URL structure
   - What it supports (title, editor, thumbnail, excerpt)
   - Related taxonomies
   - Has single pages? Has archive?

2. Add to spec under `### Post Types`:

```markdown
- **slug** - Description
  - URL: /url-structure/%postname%/
  - Dashicon: dashicons-xxx
  - Supports: title, editor, thumbnail, excerpt
  - Taxonomies: tax1, tax2
  - Single: yes/no
  - Archive: /url/ or no
```

### Taxonomies

1. Ask user for:
   - Taxonomy name/slug
   - Purpose
   - Which post types it applies to
   - Hierarchical (like categories) or flat (like tags)?
   - Has archive pages?

2. Add to spec under `### Taxonomies`:

```markdown
- **slug** - Description
  - Post types: type1, type2
  - Hierarchical: yes/no
  - Has archive: yes/no
```

### ACF Fields (Post Type Fields)

For fields attached to post types (not components):

1. Ask user for field requirements
2. Add under relevant page/post type in `## Pages and Routes`:

```markdown
### Post Type Single
- Fields:
  - **field_name** (type) - Description
  - **field_name** (type, option: value) - With options
```

## Common Dashicons

| Icon | Use For |
|------|---------|
| `dashicons-admin-post` | Articles, news |
| `dashicons-admin-page` | Pages |
| `dashicons-calendar` | Events |
| `dashicons-location` | Locations, venues |
| `dashicons-portfolio` | Projects, collections |
| `dashicons-media-audio` | Music, audio |
| `dashicons-video-alt3` | Videos |
| `dashicons-groups` | People, team |
| `dashicons-cart` | Products |

## Relationships

When post types relate to each other:

```markdown
- **related_posts** (relationship, post_type: other_type) - Description
- **parent** (post_object, post_type: parent_type, bidirectional) - Parent item
```

Bidirectional means ACF syncs both directions.

## Prompt Template

```
Define **{Post Type/Taxonomy}**:
- What content does it hold?
- URL structure?
- Related taxonomies/post types?
```
