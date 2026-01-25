# Website Specification

## Overview

**Title**: <!-- e.g., Acme Corp -->
**Live URL**: <!-- e.g., https://acme.com -->
**Staging URL**: <!-- e.g., https://staging.acme.com -->
**PHP Version**: 8.2+

<!-- Brief description of the website's purpose and target audience -->

---

## Required Plugins

<!-- All plugins auto-update. Add/remove as needed. -->

- **ACF Pro** - Custom fields and Gutenberg blocks
- **Yoast SEO** - SEO management

---

## Content Types

<!--
  Each content type includes:
  - Basic config (URL, dashicon, supports, taxonomies)
  - Archive routing (if applicable)
  - Custom fields (if any)

  Format:
  ### slug
  Description.

  - URL: /url-structure/%postname%/
  - Dashicon: dashicons-xxx
  - Supports: title, editor, thumbnail, excerpt, etc.
  - Taxonomies: category, post_tag, custom-tax

  **Archive** (/archive-url/)
  - Template: Listing
  - Route: decorate:post_type:slug

  **Fields:**
  - **field_name** (type) - Description
-->

### news_article
News articles and blog posts.

- URL: /news/%postname%/
- Dashicon: dashicons-admin-post
- Supports: title, editor, thumbnail, excerpt
- Taxonomies: category, post_tag

**Archive** (/news/)
- Template: Listing
- Route: decorate:post_type:news_article

---

### page
Static pages.

- URL: /%pagename%/
- Dashicon: dashicons-admin-page
- Supports: title, editor, thumbnail
- Taxonomies: none
- Has archive: no

<!-- Example custom content type:
### event
Events and workshops.

- URL: /events/%postname%/
- Dashicon: dashicons-calendar-alt
- Supports: title, editor, thumbnail, excerpt
- Taxonomies: location, event_type

**Archive** (/events/)
- Template: Listing
- Route: decorate:post_type:event

**Fields:**
- **start_date** (date_picker) - Event start date
- **end_date** (date_picker) - Event end date
- **venue** (text) - Venue name
-->

---

## Taxonomies

<!--
  Each taxonomy includes:
  - Basic config (post types, hierarchical)
  - Archive routing (if applicable)

  Format:
  ### slug
  Description.

  - Post types: news_article, event
  - Hierarchical: yes/no

  **Archive** (/archive-url/%slug%/)
  - Template: Listing
  - Route: decorate:taxonomy:slug
-->

### category
Post categories.

- Post types: news_article
- Hierarchical: yes

**Archive** (/news/category/%slug%/)
- Template: Listing
- Route: decorate:taxonomy:category

---

### post_tag
Post tags.

- Post types: news_article
- Hierarchical: no

**Archive** (/news/tag/%slug%/)
- Template: Listing
- Route: decorate:taxonomy:post_tag

<!-- Example custom taxonomy:
### location
Event locations.

- Post types: event
- Hierarchical: yes

**Archive** (/events/location/%slug%/)
- Template: Listing
- Route: decorate:taxonomy:location
-->

---

## Standalone Routes

<!--
  Pages and routes not tied to content type archives.

  Format:
  ### Page Name (URL)
  - Template: Default | Article | Listing | etc.
  - Route: decorate:search | decorate:404 | route (for owned routes)
  - Fields:
    - **field_name** (type) - Description
-->

### Homepage (/)
- Template: Default

### Search Results (/search/)
- Template: Default
- Route: decorate:search

### 404
- Template: Default
- Route: decorate:404

<!-- Example standalone pages:
### About (/about/)
- Template: Article

### Contact (/contact/)
- Template: Default
- Fields:
  - **address** (textarea) - Physical address
  - **phone** (text) - Contact phone number
  - **email** (email) - Contact email

### Account (/account/)
- Template: Default
- Route: route (owned)
-->

---

## Site Settings

<!--
  Global settings stored in ACF options page.

  Format:
  - **field_name** (type) - Description
  - **field_name** (type, option: value) - With options
  - **field_name** (group)
    - **nested_field** (type)

  Common types: text, textarea, wysiwyg, image, file, gallery, select,
  checkbox, radio, true_false, link, page_link, post_object, relationship,
  taxonomy, user, google_map, date_picker, color_picker, group, repeater
-->

- **logo** (image, return: array) - Site logo
- **logo_alt** (image, return: array) - Alternative logo (e.g., white version)
- **footer_text** (textarea) - Footer copyright text
- **social_links** (repeater)
  - **platform** (select: facebook, twitter, instagram, linkedin, youtube)
  - **url** (url)

---

## Menus

<!--
  Format:
  - **menu_location** - Description of where it appears
-->

- **primary** - Main navigation in site header
- **footer** - Footer navigation links

<!-- Example:
- **mobile** - Mobile-specific navigation menu
- **social** - Social media links in footer
-->

---

## Components

<!--
  Each component includes:
  - Name and block status
  - Description
  - Full ACF field group definition

  Block status: [Block] = ACF Gutenberg block, [Partial] = PHP partial only
-->

### Page Header [Block]

Full-width hero section with heading, optional background image, and CTA.

**Fields:**
- **heading** (text) - Main heading
- **subheading** (textarea) - Supporting text
- **background_image** (image, return: array)
- **background_overlay** (true_false, default: true) - Dark overlay on image
- **cta** (group)
  - **link** (link) - Button link
  - **style** (select: primary, secondary, default: primary)
  - Conditional: show if `link` is not empty

### Card [Block]

Content card with image, title, excerpt, and link.

**Fields:**
- **image** (image, return: array)
- **title** (text)
- **excerpt** (textarea)
- **link** (link)
- **style** (select: default, featured, minimal)

<!-- Add more components following the same format -->

---

## Integrations

<!-- Third-party services and APIs -->

<!-- Example:
- **Google Maps** - Embedded maps on contact page
  - API Key location: Theme Options > API Keys
- **Mailchimp** - Newsletter signup
  - List ID: abc123
  - Connected forms: Footer newsletter, Blog sidebar
- **HubSpot** - CRM integration
  - Forms submit to HubSpot via API
-->

---

## Other Functionality

<!-- Custom features, cron jobs, CLI commands, special behaviors -->

<!-- Example:
- **Event expiry** - Events automatically unpublished 24h after end date (wp-cron)
- **Import CLI** - `wp import-events` pulls events from external API
- **Member area** - Password-protected pages for logged-in users
-->
