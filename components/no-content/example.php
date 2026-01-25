<?php

/**
 * NoContent Component Examples
 */

use Geum\Components\NoContent;

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Default No Content</h2>
    <p class="component-example-section__description">Default "no content found" message.</p>
    <div class="component-example-section__preview">
        <?= NoContent::make(); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Custom Message</h2>
    <p class="component-example-section__description">No content with custom message.</p>
    <div class="component-example-section__preview">
        <?= NoContent::make(
            content: ['message' => 'No results found for your search. Please try different keywords.'],
        ); ?>
    </div>
</section>
