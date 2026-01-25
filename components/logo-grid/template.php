<?php
use Geum\Components\Image;

?>

<?php if (! empty($this->items)) { ?>
    <section class="<?= classes('logo-grid', 'cards', 'wp-block', 'animate', $this->classes) ?>" <?= attributes($this->attributes) ?>>
        <div class="logo-grid__inner content-width-fluid-lg">
            <?php if (! empty($this->heading) || ! empty($this->subheading)) { ?>
                <div class="logo-grid__header">
                    <?php if (! empty($this->heading)) { ?>
                        <?= \Geum\Components\Heading::make(
                            heading: $this->heading,
                            classes: ['logo-grid__heading'],
                        ); ?>
                    <?php } ?>

                    <?php if (! empty($this->subheading)) { ?>
                        <div class="logo-grid__subheading">
                            <?= wp_kses_post($this->subheading) ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

            <div class="logo-grid__items">
                <?php foreach ($this->items as $item) { ?>
                    <?php if (! empty($item['link'])) { ?>
                        <?= \Geum\Components\Link::make(...array_merge($item['link'], [
                            'classes' => ['logo-grid__item', 'img-fit'],
                            'content' => Image::make(...$item['image']),
                            'content_filter' => false,
                        ])); ?>
                    <?php } else { ?>
                        <div class="logo-grid__item img-fit">
                            <?= Image::make(...$item['image']); ?>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>
