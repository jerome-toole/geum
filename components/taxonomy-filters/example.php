<?php

/**
 * TaxonomyFilters Component Examples
 */

use Geum\Components\TaxonomyFilters;

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Category Filters</h2>
    <p class="component-example-section__description">Filter buttons for categories.</p>
    <div class="component-example-section__preview">
        <?= TaxonomyFilters::make(taxonomy: 'category'); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Tag Filters</h2>
    <p class="component-example-section__description">Filter buttons for post tags.</p>
    <div class="component-example-section__preview">
        <?= TaxonomyFilters::make(taxonomy: 'post_tag'); ?>
    </div>
</section>
