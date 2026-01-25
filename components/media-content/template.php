<div class="<?= classes('media-content', 'wp-block', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="media-content__inner content-width-lg">
        <div class="media-content__content">
            <?php if (! empty($this->heading)) { ?>
                <?= \Geum\Components\Heading::make(
                    heading: $this->heading,
                    classes: ['media-content__heading'],
                ); ?>
            <?php } ?>

            <?php if (! empty($this->subheading)) { ?>
                <?= \Geum\Components\Heading::make(
                    heading: $this->subheading,
                    el: 'h3',
                    classes: ['media-content__subheading'],
                ); ?>
            <?php } ?>

            <?php if (! empty($this->content)) { ?>
                <?= wp_kses_post($this->content); ?>
            <?php } ?>

            <?php if (! empty($this->button_1)) { ?>
                <div class="flex-list">
                    <?= \Geum\Components\Link::make(...$this->button_1); ?>
                </div>
            <?php } ?>
        </div>

        <?php if (! empty($this->media) || ! empty($this->image)) { ?>
            <div class="media-content__media img-fit">
                <?php if ($this->media_type === 'video' && ! empty($this->video)) {
                    echo \Geum\Components\VideoItem::make(...$this->video);
                } elseif ($this->media_type === 'image' && ! empty($this->image)) {
                    echo \Geum\Components\Image::make(...$this->image);
                } ?>
            </div>
        <?php } ?>
    </div>
</div>
