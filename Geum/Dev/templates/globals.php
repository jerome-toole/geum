<?php
/**
 * Globals style guide template.
 *
 * @var array $colors Theme colors from WordPress palette
 */
?>
<section class="style-guide__section">
    <h2>Design Tokens</h2>

    <?php if (! empty($colors)): ?>
        <h3>Colors</h3>
        <div class="dev-color-swatches" style="display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;">
            <?php foreach ($colors as $color): ?>
                <div style="text-align: center;">
                    <div style="width: 80px; height: 80px; background: <?= esc_attr($color['color']); ?>; border: 1px solid #ccc; border-radius: 4px;"></div>
                    <small><?= esc_html($color['name'] ?? $color['slug']); ?></small><br>
                    <code style="font-size: 0.75rem;"><?= esc_html($color['color']); ?></code>
                </div>
            <?php endforeach; ?>
        </div>

        <h3>Color Contexts</h3>
        <p style="margin-bottom: 1rem;">Colors can be defined with a matching foreground colour and additional custom properties. A color context can be set with the <code>.color-context-{color}</code> utility.</p>
        <div style="display: grid; gap: 1rem; margin-bottom: 2rem;">
            <?php foreach ($colors as $color): ?>
                <div class="color-context-<?= esc_attr($color['slug']); ?>" style="padding: 1.5rem; border-radius: 0.5rem;">
                    <strong><?= esc_html($color['name'] ?? $color['slug']); ?></strong>
                    <code style="opacity: 0.7;">.color-context-<?= esc_attr($color['slug']); ?></code>
                    <p style="margin: 0.5rem 0 0;">Sample text with <a href="#">a link</a> to demonstrate foreground and link colors.</p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No theme colors defined in theme.json</p>
    <?php endif; ?>
</section>
