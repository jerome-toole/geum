<?php

if (! empty($this->content['message'])) { ?>
    <section class="<?= classes('no-content', 'wp-block', $this->classes) ?>" <?= attributes($this->attributes) ?>>
        <p class="no-content__message is-style-type-h4">
            <?= wp_kses_post($this->content['message']); ?>
        </p>
    </section>
<?php }
