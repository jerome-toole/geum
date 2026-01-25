import CookieNotice from './scripts/CookieNotice.js';

window.addEventListener('DOMContentLoaded', () => {
    const element = document.querySelector('.cookie-notice');

    if (element) {
        new CookieNotice(element);
    }
});
