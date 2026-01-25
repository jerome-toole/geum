<?php if (! empty($this->items)) { ?>
    <div class="<?= classes('taxonomy-filters', 'wp-block', $this->classes) ?>" <?= attributes($this->attributes) ?>>
        <?php if (! empty($this->label)) { ?>
            <div class="taxonomy-filters__label">
                <span><?= wp_kses_post($this->label) ?></span>
            </div>
        <?php } ?>

        <ul class="taxonomy-filters__list flex-list">
            <?php foreach ($this->items as $item) { ?>
                <li class="taxonomy-filters__item-wrap">
                    <?= \Geum\Components\Link::make(...$item); ?>
                </li>
            <?php }  ?>
        </ul>
    </div>
<?php } ?>
