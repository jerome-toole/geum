<?php

/**
 * Heading Component Examples
 */

use Geum\Components\Heading;

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Heading Levels</h2>
    <p class="component-example-section__description">Different heading levels from h1 to h6.</p>
    <div class="component-example-section__preview">
        <?= Heading::make(el: 'h1', heading: 'Heading Level 1'); ?>
        <?= Heading::make(el: 'h2', heading: 'Heading Level 2'); ?>
        <?= Heading::make(el: 'h3', heading: 'Heading Level 3'); ?>
        <?= Heading::make(el: 'h4', heading: 'Heading Level 4'); ?>
        <?= Heading::make(el: 'h5', heading: 'Heading Level 5'); ?>
        <?= Heading::make(el: 'h6', heading: 'Heading Level 6'); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Heading with Link</h2>
    <p class="component-example-section__description">Heading that wraps the text in a link.</p>
    <div class="component-example-section__preview">
        <?= Heading::make(
            el: 'h2',
            heading: 'Click me to learn more',
            link: 'https://example.com',
        ); ?>
        <?= Heading::make(
            el: 'h3',
            heading: 'Opens in new tab',
            link: 'https://example.com',
            target: '_blank',
        ); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Heading with ID</h2>
    <p class="component-example-section__description">Heading with an ID for anchor links.</p>
    <div class="component-example-section__preview">
        <?= Heading::make(
            el: 'h2',
            heading: 'Section with Anchor',
            id: 'section-anchor',
        ); ?>
    </div>
</section>
