<?php
$inner_classes = ['site-main__inner'];
if (! empty($this->content_flow)) {
    $inner_classes[] = 'content-flow';
}
?>
<main class="<?= classes('site-main', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <<?= esc_html($this->inner_el); ?> class="<?= classes($inner_classes) ?>">

        <?php if (! empty($this->header) || ! empty($this->content)) { ?>
            <div class="site-main__content content-grid content-flow">
                <?php if (! empty($this->header)) { ?>
                    <?= $this->header; ?>
                <?php } ?>

                <?php if (! empty($this->content)) { ?>
                    <?= $this->content; ?>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if (! empty($this->footer)) { ?>
            <footer class="site-main__footer">
                <?= $this->footer; ?>
            </footer>
        <?php } ?>
    </<?= esc_html($this->inner_el); ?>>
</main>
