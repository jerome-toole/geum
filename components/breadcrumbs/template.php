<?php if (function_exists('yoast_breadcrumb')) { ?>
    <nav class="<?= classes('breadcrumbs', $this->classes) ?>" <?= attributes($this->attributes) ?>>
        <?php \yoast_breadcrumb('', ''); ?>
    </nav>
<?php } ?>
