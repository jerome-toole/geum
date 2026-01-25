<?php

/**
 * Element Component Examples
 */

use Geum\Components\Element;

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Basic Element</h2>
    <p class="component-example-section__description">Default div with content.</p>
    <div class="component-example-section__preview">
        <?= Element::make(content: 'This is a div element with some content.'); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Different Elements</h2>
    <p class="component-example-section__description">Various HTML element types.</p>
    <div class="component-example-section__preview">
        <?= Element::make(el: 'p', content: 'This is a paragraph element.'); ?>
        <?= Element::make(el: 'span', content: 'This is a span element.'); ?>
        <?= Element::make(el: 'article', content: 'This is an article element.'); ?>
        <?= Element::make(el: 'section', content: 'This is a section element.'); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Element with Classes</h2>
    <p class="component-example-section__description">Element with custom CSS classes.</p>
    <div class="component-example-section__preview">
        <?= Element::make(
            el: 'div',
            content: 'Styled element',
            classes: ['custom-class', 'another-class'],
        ); ?>
    </div>
</section>
