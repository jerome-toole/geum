<?php
/**
 * Content Flow Test Page
 *
 * Tests the .content-flow wrapper with realistic WP block content.
 * The site-main already wraps everything in .content-flow (content_flow: true by default).
 */
$lorem = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.';
$lorem_short = 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.';
?>

<style>
    .bc-label {
        display: inline-block;
        padding: 0.2em 0.5em;
        background: #1a1a2e;
        color: #fff;
        font-size: 0.7rem;
        font-family: monospace;
        border-radius: 3px;
        margin-bottom: 0.5rem;
        opacity: 0.7;
    }
    .bc-block-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 120px;
        background: linear-gradient(135deg, var(--color-brand-2, #e8e8f0) 0%, var(--color-brand-3, #d0d0e8) 100%);
        font-family: monospace;
        font-size: 0.85rem;
        opacity: 0.7;
    }
    .bc-block-placeholder--tall {
        min-height: 200px;
    }
</style>

<!-- ============================================================
     Prose elements — max-width constraint + spacing
     ============================================================ -->

<span class="bc-label">prose: p, h1–h6, ul, ol</span>

<h2>Heading 2 inside content-flow</h2>
<p><?= $lorem ?></p>
<p><?= $lorem_short ?></p>

<h3>Heading 3</h3>
<p><?= $lorem_short ?></p>

<ul>
    <li>Unordered list item one</li>
    <li>Unordered list item two</li>
    <li>Unordered list item three with longer text <?= $lorem_short ?></li>
</ul>

<ol>
    <li>Ordered list item one</li>
    <li>Ordered list item two</li>
    <li>Ordered list item three</li>
</ol>

<!-- ============================================================
     Standard WP block — --flow-space spacing (var(--space-layout))
     ============================================================ -->

<span class="bc-label">wp-block (standard, no alignment)</span>
<div class="wp-block-group">
    <div class="bc-block-placeholder">wp-block-group (standard)</div>
</div>

<!-- ============================================================
     WP block — alignwide
     ============================================================ -->

<span class="bc-label">wp-block.alignwide</span>
<div class="wp-block-group alignwide">
    <div class="bc-block-placeholder">wp-block-group.alignwide</div>
</div>

<p><?= $lorem_short ?></p>

<!-- ============================================================
     WP block — alignfull (no background)
     ============================================================ -->

<span class="bc-label">wp-block.alignfull (no background)</span>
<div class="wp-block-group alignfull">
    <div class="bc-block-placeholder bc-block-placeholder--tall">wp-block-group.alignfull</div>
</div>

<p><?= $lorem_short ?></p>

<!-- ============================================================
     Two consecutive alignfull blocks with backgrounds
     — should be pulled together (negative margin)
     ============================================================ -->

<span class="bc-label">Two consecutive alignfull.has-background — should touch (pull-together)</span>
<div class="wp-block-group alignfull has-background has-brand-1-background-color color-context-brand-1">
    <div style="padding: var(--space-layout, 3rem); text-align: center;">
        <strong>First alignfull with background</strong>
        <p><?= $lorem_short ?></p>
    </div>
</div>
<div class="wp-block-group alignfull has-background has-brand-2-background-color color-context-brand-2">
    <div style="padding: var(--space-layout, 3rem); text-align: center;">
        <strong>Second alignfull with background — pulled up</strong>
        <p><?= $lorem_short ?></p>
    </div>
</div>

<p><?= $lorem_short ?></p>

<!-- ============================================================
     wp-block-group — nested content-flow
     Children get the same margin-top spacing as the outer context
     ============================================================ -->

<span class="bc-label">wp-block-group (nested content-flow — stacked children)</span>
<div class="wp-block-group">
    <div class="bc-block-placeholder">Child block 1</div>
    <div class="bc-block-placeholder">Child block 2</div>
    <p><?= $lorem_short ?></p>
    <div class="bc-block-placeholder">Child block 3</div>
</div>

<p><?= $lorem_short ?></p>

<!-- ============================================================
     wp-block-group.is-layout-grid — columns above md, stacked below
     Override: CSS grid with gap (var(--flow-space)) replaces flow spacing
     ============================================================ -->

<span class="bc-label">wp-block-group.is-layout-grid (2 columns above md)</span>
<div class="wp-block-group is-layout-grid">
    <div class="bc-block-placeholder">Column 1</div>
    <div class="bc-block-placeholder">Column 2</div>
</div>

<p><?= $lorem_short ?></p>

<span class="bc-label">wp-block-group.is-layout-grid (3 columns — --cols: 3)</span>
<div class="wp-block-group is-layout-grid" style="--cols: 3">
    <div class="bc-block-placeholder">Column 1</div>
    <div class="bc-block-placeholder">Column 2</div>
    <div class="bc-block-placeholder">Column 3</div>
</div>

<p><?= $lorem_short ?></p>

<!-- ============================================================
     Section elements (section class)
     ============================================================ -->

<span class="bc-label">section.alignfull.has-background (last-child footer pull)</span>
<section class="section alignfull has-background has-brand-3-background-color color-context-brand-3">
    <div style="padding: var(--space-layout, 3rem); text-align: center;">
        <strong>Last alignfull.has-background — pulls footer up</strong>
        <p><?= $lorem_short ?></p>
    </div>
</section>
