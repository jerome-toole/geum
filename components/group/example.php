<?php

/**
 * Group Component Examples
 *
 * Note: Group is primarily used as a Gutenberg block wrapper.
 */

use Geum\Components\Group;

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Basic Group</h2>
    <p class="component-example-section__description">Group container for block content.</p>
    <div class="component-example-section__preview">
        <?= Group::make(
            content: '<h2>Group Heading</h2><p>This is content inside a group component. Groups are typically used in the block editor to wrap content with optional background images.</p>',
        ); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Group with Background Color</h2>
    <p class="component-example-section__description">Group with overlay color specified.</p>
    <div class="component-example-section__preview">
        <?= Group::make(
            content: '<h2>Styled Group</h2><p>Group with background styling applied.</p>',
            background_color: 'brand-1',
        ); ?>
    </div>
</section>
