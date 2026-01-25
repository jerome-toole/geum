<?php if (! empty($this->items)) { ?>
    <section class="<?= classes('cards', 'wp-block', 'animate', $this->classes) ?>" <?= attributes($this->attributes) ?>>
        <div class="cards__inner content-width-lg">
            <?php if (! empty($this->heading) || ! empty($this->subheading)) { ?>
                <div class="cards__header">
                    <?php if (! empty($this->heading)) { ?>
                        <?= \Geum\Components\Heading::make(
                            heading: $this->heading,
                            classes: ['cards__heading'],
                        ); ?>
                    <?php } ?>

                    <?php if (! empty($this->subheading)) { ?>
                        <div class="cards__subheading">
                            <?= wp_kses_post($this->subheading); ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

            <div class="cards__items">
                <?php foreach ($this->items as $key => $card) { ?>
                    <?= \Geum\Components\Card::make(...$card); ?>
                <?php } ?>
            </div>

            <?php if (! empty($this->button)) { ?>
                <div class="cards__footer">
                    <div class="cards__more-link">
                        <?= \Geum\Components\Link::make(...$this->button); ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>
<?php } ?>
