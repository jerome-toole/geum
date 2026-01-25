<section class="<?= classes('banner', 'wp-block', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="banner__inner content-width-lg margin-trim">
        <?php if (! empty($this->image)) { ?>
            <div class="banner__image">
                <?= \Geum\Components\Image::make(...$this->image); ?>
            </div>
        <?php } ?>

        <?php if (! empty($this->message)) { ?>
            <div class="banner__message margin-trim">
                <?= wp_kses_post($this->message); ?>
            </div>
        <?php } ?>
    </div>

    <?php if (! empty($this->show_close_button)) { ?>
        <button class="banner__close" type="button">
            <span class="screen-reader-text">Close</span>
        </button>
    <?php } ?>
</section>
