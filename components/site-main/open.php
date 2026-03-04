<?php
$content_classes = [];
if (! empty($args['content_flow'])) {
    $content_classes[] = 'content-flow';
}
?>
<main class="<?= classes('site-main', $args['classes'] ?? []) ?>" <?= attributes($args['attributes'] ?? []) ?>>
    <<?= esc_html($args['inner_el'] ?? 'div') ?> class="site-main__inner">
        <div class="<?= classes('site-main__content', 'content-grid', $content_classes) ?>">
