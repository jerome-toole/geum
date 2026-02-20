<?php

/**
 * CookieConsent Component Examples
 */

use Geum\Components\CookieConsent;

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Cookie Consent</h2>
    <p class="component-example-section__description">GDPR-compliant cookie consent banner.</p>
    <div class="component-example-section__preview">
        <?= CookieConsent::make(
            content: 'We use cookies to improve your experience. By using our site, you agree to our use of cookies.',
            accept_button_text: 'Accept All',
            accept_button_text_additional_context: 'cookies',
            reject_button_text: 'Reject All',
            reject_button_text_additional_context: 'cookies',
        ); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Cookie Consent with Privacy Link</h2>
    <p class="component-example-section__description">Cookie consent referencing privacy policy.</p>
    <div class="component-example-section__preview">
        <?= CookieConsent::make(
            content: 'This site uses cookies. Read our <a href="/privacy-policy">Privacy Policy</a> to learn more.',
            accept_button_text: 'Got it!',
            accept_button_text_additional_context: 'I understand',
            reject_button_text: 'No thanks',
            reject_button_text_additional_context: 'decline cookies',
        ); ?>
    </div>
</section>
