<header class="<?= classes('page-header', 'wp-block', 'alignfull', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <?php if ($this->show_breadcrumbs) { ?>
        <?= \Geum\Components\Breadcrumbs::make(); ?>
    <?php } ?>

    <div class="page-header__inner">
        <?php if (! empty($this->image) && $this->image_position === 'mini') { ?>
            <div class="page-header__mini-image">
                <div class="page-header__mini-image-inner img-fit">
                    <?= $this->image; ?>
                </div>
            </div>
        <?php } ?>

        <div class="page-header__content">
            <?php if (! empty($this->heading)) { ?>
                <?= \Geum\Components\Heading::make(...$this->heading); ?>
            <?php } ?>

            <?php if (! empty($this->subheading)) { ?>
                <div class="page-header__subheading">
                    <?= wp_kses_post($this->subheading); ?>
                </div>
            <?php } ?>

            <?php if (! empty($this->meta)) { ?>
                <div class="page-header__meta is-style-type-meta">
                    <?= wp_kses_post($this->meta); ?>
                </div>
            <?php } ?>

            <?php if (! empty($this->labels)) { ?>
                <div class="page-header__labels">
                    <div class="page-header__labels__items flex-list">
                        <?php foreach ($this->labels as $label) {
                            echo \Geum\Components\Link::make(
                                title: $label['name'],
                                url: $label['url'],
                                classes: [
                                    'btn',
                                    'btn--label',
                                ],
                            );
                        } ?>
                    </div>
                </div>
            <?php } ?>

            <?php if (! empty($this->buttons)) { ?>
                <div class="page-header__buttons">
                    <ul class="flex-list">
                        <?php foreach ($this->buttons as $button) { ?>
                            <li class="flex-list__item">
                                <?= \Geum\Components\Link::make(...$button); ?>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>
        </div>

        <?php if (! empty($this->image) && $this->image_position === 'inset') { ?>
            <div class="page-header__inset-image">
                <div class="page-header__inset-image-inner img-fit">
                    <?= $this->image; ?>
                </div>
            </div>
        <?php } ?>
    </div>
</header>
