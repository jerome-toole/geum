import { getCookie, setCookie } from '../../../assets/scripts/helpers/cookies.js';

export default class CookieNotice {
    constructor(element) {
        this.el = element;

        this.bannerEl = this.el.querySelector('.cookie-notice__banner');
        this.bannerEl.setAttribute('tabindex', -1);

        this.acceptButton = this.el.querySelector('.js-cookie-notice-accept');
        this.rejectButton = this.el.querySelector('.js-cookie-notice-reject');
        this.togglers = document.querySelectorAll('.js-cookie-notice-toggler');

        this.prevActiveElement = null;

        this.cookieLifetime = 365; // Days
        this.cookieName = 'cookies';
        this.validCookieValues = ['accept', 'reject'];

        this.init();
    }

    init() {
        if (this.validCookieValues.indexOf(getCookie(this.cookieName)) === -1) {
            this.setActive(true);
        } else {
            this.prevActiveElement = document.activeElement;
            this.setActive(false);
        }

        this.acceptButton.addEventListener('click', () => {
            this.setCookieChoice('accept');

            // Google Consent Mode v2 — update immediately for the current session
            if (typeof gtag === 'function') {
                gtag('consent', 'update', {
                    analytics_storage:  'granted',
                    ad_storage:         'granted',
                    ad_user_data:       'granted',
                    ad_personalization: 'granted',
                });
            }

            // Activate any consent-gated scripts already in the page
            this.activateConsentScripts();

            this.setActive(false);
        });

        this.rejectButton.addEventListener('click', () => {
            this.setCookieChoice('reject');
            this.setActive(false);
        });

        this.togglers.forEach((element) => {
            element.setAttribute('aria-expanded', this.isActive());
            element.setAttribute('aria-controls', this.el.id);
            element.addEventListener('click', this.handleTogglerClick.bind(this));
        });

        document.addEventListener('click', (e) => {
            const trigger = e.target.closest('.reset-cookie-preferences');
            if (!trigger) return;
            e.preventDefault();
            setCookie(this.cookieName, '', -1);
            window.location.reload();
        });
    }

    /**
     * Set the active state of the notice
     *
     * @param {boolean} active Whether or not we want to set the notice as active
     */
    setActive(active) {
        if (active === true) {
            this.prevActiveElement = document.activeElement;
            this.el.removeAttribute('aria-hidden');

            this.bannerEl.focus();

            this.togglers.forEach((element) => {
                element.setAttribute('aria-expanded', true);
            });
        } else {
            this.prevActiveElement.focus();
            this.el.setAttribute('aria-hidden', true);

            this.togglers.forEach((element) => {
                element.setAttribute('aria-expanded', false);
            });
        }
    }

    /**
     * Toggle the active state
     *
     */
    toggleActive() {
        this.setActive(!this.isActive());
    }

    /**
     * Check whether the notice is currently active
     *
     */
    isActive() {
        return !this.el.hasAttribute('aria-hidden');
    }

    /**
     * Handle the given choice (accept/reject)
     *
     * @param {boolean} choice Whether the user has accepted/rejected site cookies.
     */
    setCookieChoice(choice) {
        setCookie(this.cookieName, choice, this.cookieLifetime);
    }

    /**
     * Activate any inert consent-gated scripts in the page.
     * Scripts are output as <script type="text/plain" data-cookie-consent> by PHP
     * when the user hasn't yet consented. This method clones them as real scripts
     * so they execute immediately after the user accepts.
     */
    activateConsentScripts() {
        document.querySelectorAll('script[type="text/plain"][data-cookie-consent]').forEach((inert) => {
            const script = document.createElement('script');
            [...inert.attributes].forEach(({ name, value }) => {
                if (name !== 'type') script.setAttribute(name, value);
            });
            script.textContent = inert.textContent;
            document.head.appendChild(script);
            inert.remove();
        });
    }

    /**
     * Handle a toggler click
     *
     */
    handleTogglerClick() {
        this.toggleActive();
    }
}
