# Component Definition

Define blocks and partials in the website spec from screenshots and descriptions.

## Workflow

### Single Component

1. Ask user for:
   - Component name
   - Type: Block (ACF Gutenberg) or Partial (PHP only)
   - Screenshot (if available)
   - Brief description

2. Analyze screenshot for:
   - Visual structure
   - Functionality
   - Text fields (headings, body, labels)
   - Images (with captions/credits)
   - Links/CTAs
   - Repeating elements → repeater fields
   - Conditional elements

3. Add to `.docs/_WEBSITE-SPEC.md` under `## Components`:

```markdown
### Component Name [Block|Partial]
Brief description of component purpose.

**Fields:**
- **field_name** (type) - Description
- **field_name** (repeater)
  - **nested_field** (type) - Description
```

### Batch Components

When multiple components need defining:

1. List all undefined components in spec
2. For each, prompt: "Describe **{name}** or provide screenshot"
3. Wait for response before moving to next
4. Fill in spec entry

## Field Types Reference

| Type | Use For |
|------|---------|
| `text` | Short text, labels, headings |
| `textarea` | Multi-line plain text |
| `wysiwyg` | Rich text with formatting |
| `image` | Single image (add `return: array`) |
| `link` | URL with title |
| `true_false` | Toggle options |
| `select` | Dropdown (list options) |
| `repeater` | Repeating content groups |
| `relationship` | Link to posts |
| `post_object` | Single post link |
| `taxonomy` | Term selection |
| `group` | Nested field group |

## Common Patterns

### Promo/Card
```markdown
- **preheading** (text)
- **heading** (text)
- **body** (wysiwyg)
- **link** (link)
- **image** (image, return: array)
```

### Image with Metadata
```markdown
- **image** (image, return: array)
- **caption** (text)
- **credit** (text)
```

### Repeater with Items
```markdown
- **heading** (text) - Section heading
- **items** (repeater)
  - **title** (text)
  - **description** (textarea)
```

### Related Posts (auto-populate)
```markdown
**Auto-population logic:**
- On {context}: {logic}

**Fields:**
- **heading** (text, default: "Related")
- **items** (relationship, post_type: {type}) - Manual override; if empty, auto-populates
```

## Partial vs Block

| Partial | Block |
|---------|-------|
| Data from post/context | Data from ACF fields |
| No editor UI | Gutenberg block UI |
| Used in templates | Placed in editor |
| e.g., Work Page Header | e.g., Promo |

## Prompt Template

When asking for component info:

```
Define **{Component Name}**:
- Description or screenshot?
- Block (editor placeable) or Partial (template only)?
```
