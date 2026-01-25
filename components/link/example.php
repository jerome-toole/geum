<?php

/**
 * Link Component Examples
 */

use Geum\Components\Link;

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Basic Link</h2>
    <p class="component-example-section__description">Simple link with title and URL.</p>
    <div class="component-example-section__preview">
        <?= Link::make(title: 'Visit Example', url: 'https://example.com'); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Link with Target</h2>
    <p class="component-example-section__description">Links that open in new tab (automatically adds rel="noopener").</p>
    <div class="component-example-section__preview">
        <?= Link::make(
            title: 'Opens in new tab',
            url: 'https://example.com',
            target: '_blank',
        ); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Link with Assistive Text</h2>
    <p class="component-example-section__description">Links with screen reader text for accessibility.</p>
    <div class="component-example-section__preview">
        <?= Link::make(
            title: 'Read more',
            url: '/article',
            assistive_text_after: ' about our services',
        ); ?>
        <?= Link::make(
            title: 'Download PDF',
            url: '/file.pdf',
            assistive_text_before: 'File: ',
        ); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Link with Prefix/Suffix</h2>
    <p class="component-example-section__description">Links with visible prefix or suffix content.</p>
    <div class="component-example-section__preview">
        <?= Link::make(
            title: 'Learn more',
            url: '/about',
            suffix: ' &rarr;',
        ); ?>
    </div>
</section>
