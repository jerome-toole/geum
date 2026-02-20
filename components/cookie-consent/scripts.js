import CookieConsent from './scripts/CookieConsent.js';

window.addEventListener('DOMContentLoaded', () => {
    const element = document.querySelector('.cookie-consent');

    if (element) {
        new CookieConsent(element);
    }
});
