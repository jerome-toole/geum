<form class="<?= classes($this->classes) ?>" <?= attributes($this->attributes) ?>>
    <label class="header-search__group input-group" for="<?= esc_attr($this->input_id); ?>">
        <input
            id="<?= esc_attr($this->input_id); ?>"
            class="header-search__input"
            type="text"
            name="s"
            aria-label="<?= esc_attr__('Search', 'geum'); ?>"
            placeholder="<?= esc_attr__('Search...', 'geum'); ?>"
            required
        >

        <button class="header-search__submit btn" type="submit">
            <span class="screen-reader-text">
                <?= esc_html__('Submit', 'geum'); ?>
            </span>
            <span class="header-search__submit__text">
                <?= esc_html__('Search', 'geum'); ?>
            </span>
        </button>
    </label>
</form>
