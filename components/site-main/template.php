<?php
$inner_classes = ['site-main__inner'];
if (! empty($this->blocks_context)) {
    $inner_classes[] = 'blocks-context';
}
?>
<main class="<?= classes('site-main', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <<?= esc_html($this->inner_el); ?> class="<?= classes($inner_classes) ?>">

        <?php if (! empty($this->header) || ! empty($this->content)) { ?>
            <div class="site-main__content page-grid">
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
