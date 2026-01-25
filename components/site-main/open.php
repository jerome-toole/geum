<?php
$inner_classes = ['site-main__inner'];
if (! empty($args['blocks_context'])) {
    $inner_classes[] = 'blocks-context';
}
?>
<main class="<?= classes('site-main', $args['classes'] ?? []) ?>" <?= attributes($args['attributes'] ?? []) ?>>
    <<?= esc_html($args['inner_el'] ?? 'div') ?> class="<?= classes($inner_classes) ?>">
        <div class="site-main__content page-grid">
