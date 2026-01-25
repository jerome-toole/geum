<?php
use Geum\Components\Image;

?>

<footer class="<?= classes('site-footer', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="site-footer__inner content-width-fluid-lg">
        <div class="site-footer__top alignwide">
            <div class="site-footer__logo">
                <?= \Geum\Components\Link::make(
                    url: home_url('/'),
                    content: \Geum\Image::get('logo-alt.svg', [
                        'alt' => get_bloginfo('name'),
                    ]),
                    content_filter: false,
                ); ?>
            </div>

            <?php if ($top_text = get_field('footer_text_top', 'option')) { ?>
                <div class="site-footer__top-text">
                    <?= wp_kses_post($top_text); ?>
                </div>
            <?php } ?>

            <?= \Geum\Components\Menu::make(
                theme_location: 'footer-1',
                max_depth: 1,
                classes: [
                    'site-footer__menu',
                    'site-footer__menu-1',
                ],
                heading: true,
            ); ?>

            <?= \Geum\Components\Menu::make(
                theme_location: 'footer-2',
                max_depth: 1,
                classes: [
                    'site-footer__menu',
                    'site-footer__menu-2',
                ],
                heading: true,
            ); ?>

            <div class="site-footer__right">
                <?= \Geum\Components\SocialIcons::make(
                    // translators: 1: Social network name.
                    title: __('Visit our %s page', 'geum'),
                ); ?>

                <?php if (! empty($this->content['images'])) { ?>
                    <div class="site-footer__images flex-grid">
                        <?php foreach ($this->content['images'] as $image) { ?>
                            <?php if (! empty($image['link_args'])) { ?>
                                <?= \Geum\Components\Link::make(...$image['link_args']); ?>
                            <?php } else { ?>
                                <div class="site-footer__image img-fit">
                                    <?= Image::make(...$image['image']); ?>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="site-footer__bottom">
            <div class="site-footer__bottom__inner alignwide">
                <?php if ($bottom_text = get_field('footer_text_bottom', 'option')) { ?>
                    <div class="site-footer__bottom-text">
                        <?= wp_kses_post($bottom_text); ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</footer>
