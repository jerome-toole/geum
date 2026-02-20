<?php if (! empty($this->content)) { ?>
    <div class="<?= classes('cookie-consent', $this->classes) ?>" <?= attributes($this->attributes) ?>>
        <div class="cookie-consent__banner has-brand-1-background-color">
            <div class="cookie-consent__message">
                <?= wp_kses_post($this->content); ?>
            </div>

            <div class="cookie-consent__actions">
                <ul class="cookie-consent__actions-list flex-list">
                    <li class="cookie-consent__action">
                        <button type="button" class="btn cookie-consent__accept js-cookie-consent-accept">
                            <?= wp_kses_post($this->accept_button_text); ?>
                            <span class="screen-reader-text">
                                <?= wp_kses_post($this->accept_button_text_additional_context); ?>
                            </span>
                        </button>
                    </li>

                    <li class="cookie-consent__action">
                        <button type="button" class="btn cookie-consent__reject js-cookie-consent-reject">
                            <?= wp_kses_post($this->reject_button_text); ?>
                            <span class="screen-reader-text">
                                <?= wp_kses_post($this->reject_button_text_additional_context); ?>
                            </span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
<?php } ?>
