<?php

/**
 * Button Component Examples
 */

use Geum\Components\Button;

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Basic Button</h2>
    <p class="component-example-section__description">Simple button with text content.</p>
    <div class="component-example-section__preview">
        <?= Button::make(content: 'Click me'); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Button Types</h2>
    <p class="component-example-section__description">Different button types: button, submit, reset.</p>
    <div class="component-example-section__preview" style="display: flex; gap: 1rem;">
        <?= Button::make(content: 'Button', type: 'button'); ?>
        <?= Button::make(content: 'Submit', type: 'submit'); ?>
        <?= Button::make(content: 'Reset', type: 'reset'); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Screen Reader Text</h2>
    <p class="component-example-section__description">Button with visually hidden text for accessibility.</p>
    <div class="component-example-section__preview">
        <?= Button::make(content: 'Close menu', screen_reader_text: true); ?>
    </div>
</section>
