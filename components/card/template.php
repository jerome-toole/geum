<article class="<?= classes('g-card', 'animate-element', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="g-card__inner">
        <?php if (! empty($this->content['heading']) || ! empty($this->content['text'])) { ?>
            <div class="g-card__header">
                <?php if (! empty($this->content['heading'])) { ?>
                    <?= \Geum\Components\Heading::make(...$this->content['heading']); ?>
                <?php } ?>

                <?php if (! empty($this->content['text'])) { ?>
                    <div class="g-card__content">
                        <?= wp_kses_post($this->content['text']); ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if (! empty($this->content['meta'])) { ?>
            <div class="g-card__meta">
                <?= wp_kses_post($this->content['meta']); ?>
            </div>
        <?php } ?>

        <?php if (! empty($this->content['labels'])) { ?>
            <div class="g-card__labels">
                <div class="g-card__labels__items">
                    <?php foreach ($this->content['labels'] as $label) {
                        echo \Geum\Components\Link::make(
                            title: $label['name'],
                            url: $label['url'],
                            classes: ['btn', 'btn--label'],
                        );
                    } ?>
                </div>
            </div>
        <?php } ?>

        <?php if ($this->show_read_more && ! empty($this->content['read_more'])) {
            echo \Geum\Components\Link::make(...$this->content['read_more']);
        } ?>
    </div>

    <?php if (! empty($this->content['image'])) { ?>
        <div class="g-card__image">
            <div class="g-card__image-inner img-fit">
                <?= \Geum\Components\Image::make(...$this->content['image']); ?>
            </div>
        </div>
    <?php } ?>
</article>
