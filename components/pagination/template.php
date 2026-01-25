<?php if (! empty($this->output)) { ?>
    <div class="<?= classes('pagination', 'alignfull', 'wp-block', $this->classes) ?>" <?= attributes($this->attributes) ?>>
        <?= $this->output; ?>
    </div>
<?php } ?>
