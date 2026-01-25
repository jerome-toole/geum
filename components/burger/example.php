<?php

/**
 * Burger Component Examples
 */

use Geum\Components\Burger;

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Basic Burger</h2>
    <p class="component-example-section__description">Hamburger menu icon wrapped in a button.</p>
    <div class="component-example-section__preview">
        <button type="button" class="btn" aria-label="Toggle menu" aria-controls="main-menu" aria-expanded="false">
            <?= Burger::make(); ?>
        </button>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Burger with Custom Class</h2>
    <p class="component-example-section__description">Burger with additional styling class.</p>
    <div class="component-example-section__preview">
        <button type="button" class="btn" aria-label="Open navigation">
            <?= Burger::make(classes: ['burger--large']); ?>
        </button>
    </div>
</section>
