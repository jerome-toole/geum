import SiteHeader from './scripts/SiteHeader.js';

window.addEventListener('DOMContentLoaded', () => {
    const element = document.querySelector('.site-header');

    if (element) {
        new SiteHeader(element);
    }
});
