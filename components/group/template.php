<section class="<?= classes('group', 'wp-block', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <?php if (! empty($this->image)) { ?>
        <div class="group__background-image img-fit" aria-hidden="true">
            <?= $this->image; ?>
        </div>
    <?php } ?>

    <div class="group__inner margin-trim">
        <InnerBlocks
            template="<?= esc_attr(wp_json_encode($this->blockTemplate)); ?>"
            allowedBlocks="<?= esc_attr(wp_json_encode($this->allowedBlocks)); ?>"
        />
    </div>
</section>
