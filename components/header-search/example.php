<?php

/**
 * HeaderSearch Component Examples
 */

use Geum\Components\HeaderSearch;

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Header Search</h2>
    <p class="component-example-section__description">Search form for the site header.</p>
    <div class="component-example-section__preview">
        <?= HeaderSearch::make(); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Header Search with Custom ID</h2>
    <p class="component-example-section__description">Search form with custom input ID for JS targeting.</p>
    <div class="component-example-section__preview">
        <?= HeaderSearch::make(input_id: 'custom-search-input'); ?>
    </div>
</section>
