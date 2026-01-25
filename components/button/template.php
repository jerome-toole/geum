<button class="<?= classes('button', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <?= wp_kses_post($this->content); ?>
</button>
