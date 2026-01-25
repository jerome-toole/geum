<header class="<?= classes('site-header', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="site-header__bar">
        <?= \Geum\Components\Link::make(
            url: home_url('/'),
            classes: ['site-header__logo', 'img-fit'],
            title: \Geum\Image::get('logo-alt.svg', [
                'alt' => get_bloginfo('name'),
                'loading' => false,
                'attributes' => [
                    'data-spai-eager' => class_exists('\\ShortPixelAI') ? 'true' : null,
                ],
            ]),
            content_filter: '',
        ); ?>

        <nav class="site-header__nav">
            <?= \Geum\Components\Menu::make(
                theme_location: 'header',
                menu_id: 'main-menu-desktop',
                classes: ['site-header__navigation'],
            ); ?>

            <div class="site-header__search-desktop-wrapper">
                <button
                    class="site-header__search-toggler js-search-toggle btn btn--ghost"
                    aria-expanded="false"
                    aria-controls="site-header-search-desktop"
                    type="button">
                    <span class="btn__icon" data-show-collapsed style="--btn--icon: url('<?= staticUrl('images/icons/search.svg') ?>')"><span class="sr-only"><?= __('Open search', 'geum'); ?></span></span>
                    <span class="btn__icon" data-show-expanded style="--btn--icon: url('<?= staticUrl('images/icons/close.svg') ?>')"><span class="sr-only"><?= __('Close search', 'geum'); ?></span></span>
                </button>

                <div class="site-header__search-desktop" id="site-header-search-desktop" hidden>
                    <?= \Geum\Components\HeaderSearch::make(); ?>
                </div>
            </div>

            <?php if (! empty($this->content['call_to_action_1'])) { ?>
                <?= \Geum\Components\Link::make(...$this->content['call_to_action_1']); ?>
            <?php } ?>
        </nav>

        <div class="site-header__buttons">
            <button
                class="site-header__search-toggler js-search-toggle btn btn--ghost"
                aria-expanded="false"
                aria-controls="site-header-search-panel"
                type="button">
                <span class="btn__icon" data-show-collapsed style="--btn--icon: url('<?= staticUrl('images/icons/search.svg') ?>')"></span>
                <?= \Geum\Components\Burger::make(
                    classes: ['site-header__close-icon', 'is-open'],
                    attributes: ['data-hide-collapsed' => '']
                ); ?>
                <span class="sr-only"><?= __('Search', 'geum'); ?></span>
                <span aria-hidden="true" data-show-collapsed>Search</span>
                <span aria-hidden="true" data-hide-collapsed>Close</span>
            </button>

            <button
                class="site-header__burger js-site-header-toggle btn btn--ghost"
                aria-label="<?= __('Main menu button', 'geum') ?>"
                aria-controls="site-header-menu-panel"
                aria-expanded="false"
                type="button">
                <?= \Geum\Components\Burger::make(); ?>
                <span class="sr-only"><?= __('Main menu button', 'geum') ?></span>
                <span aria-hidden="true" data-show-collapsed>Menu</span>
                <span aria-hidden="true" data-hide-collapsed>Close</span>
            </button>
        </div>
    </div>

    <div class="site-header__panel site-header__menu-panel" id="site-header-menu-panel" inert>
        <div class="site-header__panel-content">
            <?= \Geum\Components\Menu::make(
                theme_location: 'header',
                menu_id: 'main-menu',
                classes: ['site-header__navigation'],
            ); ?>

            <?php if (! empty($this->content['call_to_action_1'])) { ?>
                <?= \Geum\Components\Link::make(...$this->content['call_to_action_1']); ?>
            <?php } ?>
        </div>
    </div>

    <div class="site-header__panel site-header__search-panel" id="site-header-search-panel" inert>
        <div class="site-header__panel-content">
            <?= \Geum\Components\HeaderSearch::make(); ?>
        </div>
    </div>
</header>
