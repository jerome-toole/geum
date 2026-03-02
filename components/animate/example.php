<?php

/**
 * Animate Component Examples
 *
 * The animate component is a CSS/JS utility — no PHP class needed.
 * Wrap content in `.animate` and mark elements with `.animate-element`.
 * The IntersectionObserver adds `.animate--play` when the wrapper enters the viewport.
 *
 * CSS variables (set inline or via class on .animate):
 *   --animate-animation   Animation name (default: fade-in)
 *   --animate-duration    Duration (default: 300ms)
 *   --animate-delay       Base delay (default: 50ms)
 *   --animate-item-delay  Per-item stagger step (default: 0ms)
 *   --animate-easing      Easing (default: ease-out)
 *   --animate-key         Item index for stagger (set on each .animate-element)
 *   --animate-translateX  X offset for fade-in-translate (default: 0)
 *   --animate-translateY  Y offset for fade-in-translate (default: 0)
 *
 * Tailwind utilities (via @theme):
 *   animate-fade-in           — standalone fade-in animation
 *   animate-fade-in-translate — standalone fade + slide animation
 */
?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Default: Fade In</h2>
    <p class="component-example-section__description">
        Wrap content in <code>.animate</code> and mark children with <code>.animate-element</code>.
        Each element fades in when the wrapper scrolls into view.
    </p>
    <div class="component-example-section__preview">
        <div class="animate">
            <p class="animate-element p-16 bg-slate-100 rounded">I fade in on scroll.</p>
        </div>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Fade In + Translate (slide up)</h2>
    <p class="component-example-section__description">
        Set <code>--animate-animation: fade-in-translate</code> and
        <code>--animate-translateY</code> to slide elements in from below.
    </p>
    <div class="component-example-section__preview">
        <div class="animate" style="--animate-animation: fade-in-translate; --animate-translateY: 24px;">
            <p class="animate-element p-16 bg-slate-100 rounded">I slide up and fade in on scroll.</p>
        </div>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Staggered Items</h2>
    <p class="component-example-section__description">
        Set <code>--animate-item-delay</code> on the wrapper and
        <code>--animate-key</code> on each child to stagger their animations.
    </p>
    <div class="component-example-section__preview" style="display: flex; gap: 1rem;">
        <div class="animate" style="--animate-animation: fade-in-translate; --animate-translateY: 20px; --animate-item-delay: 80ms; display: contents;">
            <?php foreach (['First', 'Second', 'Third', 'Fourth'] as $i => $label) { ?>
                <div
                    class="animate-element p-16 bg-slate-100 rounded flex-1 text-center"
                    style="--animate-key: <?= $i ?>;"
                >
                    <?= $label ?>
                </div>
            <?php } ?>
        </div>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Custom Duration &amp; Easing</h2>
    <p class="component-example-section__description">
        Override timing via CSS variables on the <code>.animate</code> wrapper.
    </p>
    <div class="component-example-section__preview">
        <div class="animate" style="--animate-duration: 800ms; --animate-easing: var(--ease-out-expo);">
            <p class="animate-element p-16 bg-slate-100 rounded">Slow, expo-eased fade in.</p>
        </div>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Standalone Tailwind Utility</h2>
    <p class="component-example-section__description">
        Use <code>animate-fade-in</code> or <code>animate-fade-in-translate</code> as a Tailwind utility
        without the scroll-trigger wrapper — useful for page-load animations.
    </p>
    <div class="component-example-section__preview" style="display: flex; gap: 1rem;">
        <div class="animate-fade-in p-16 bg-slate-100 rounded">animate-fade-in</div>
        <div class="animate-fade-in-translate p-16 bg-slate-100 rounded" style="--animate-translateY: 16px;">animate-fade-in-translate</div>
    </div>
</section>
