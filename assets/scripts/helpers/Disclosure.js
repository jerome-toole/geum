import dynamicElements from './dynamicElements.js';
import Focusable from './Focusable.js';

// This class is instantiated for all elements with the [data-disclosure] attribute
// It can be used to create a disclosure widget for any element that can be hidden or shown

// Example usage:
// <button aria-controls="target" aria-expanded="false">Toggle</button>
// <div id="target" hidden aria-hidden="true" data-disclosure>Content</div>

/**
 * Disclosure
 * A disclosure is a widget that enables content to be either collapsed (hidden) or expanded (visible)
 * @link https://www.w3.org/WAI/ARIA/apg/patterns/disclosure/
 * @link overview of aria-expanded: https://www.accessibility-developer-guide.com/examples/sensible-aria-usage/expanded/
 *
 * This class can be used to create a disclosure widget for any element that can be hidden or shown.
 * Either use the [data-disclosure] attribute on the target element, or instantiate the class directly if
 * you need more control over the options.
 *
 * Give the target element:
 * - [id="TARGET ELEMENT ID"]
 *
 * Give all trigger elements:
 * - [aria-controls="TARGET ELEMENT ID"]
 *
 */
export default class Disclosure {
    constructor(element, options = {}) {
        this.el = element;
        this.isExpanded = this.el.getAttribute('aria-hidden') !== 'true';

        // Set instance options by combining default options with any overrides via spread syntax.
        this.options = {
            animate: this.getBooleanDataAttribute('disclosureAnimate', false),
            animateDuration: 200,
            animateEasing: 'ease',
            collapseOnEscape: true,
            collapseOnFocusout: false,
            collapseAncestorsOnEscape: false,
            collapseOnAncestorCollapse: false,
            focusWithinOnExpand: false,
            updateChildTabIndexes: true,
            setHiddenAttribute: true,
            setInertAttribute: true,
            allowClose: true,
            expandOnHash: this.getBooleanDataAttribute('expandOnHash', true),
            disclosureGroup: this.el.dataset.disclosureGroup ?? null,
            collapseOnOtherElementsExpand: null, // Array of elements which will close the disclosure when expanded.
            on: {},
            ...options,
        };

        this.lastTrigger = null;
        this.animation = null;
        this.isClosing = false;
        this.isExpanding = false;
        this.reducedMotion = this.getReducedMotion();

        if (this.reducedMotion) {
            this.options.animate = false;
        }

        // Bail early - required elements or markup not found.
        if (!this.setUpElements()) {
            return;
        }

        // Helper object to manage focusable elements inside expandable element.
        this.focusableItems = new Focusable(this.el);

        // Setup initial event listeners.
        if (this.isExpanded) {
            this.el.addEventListener('collapsebegin', this);
            this.el.addEventListener('collapseend', this);
        } else {
            this.el.addEventListener('expandbegin', this);
            this.el.addEventListener('expandend', this);
        }

        // Handle clicks - triggers and inside & outside target.
        document.addEventListener('click', this);

        // Add global listener for expand events
        if (this.options.collapseOnOtherElementsExpand || this.options.disclosureGroup) {
            document.addEventListener('expandbegin', this.handleGlobalExpand.bind(this));
        }

        // Handle hash change for expandOnHash option
        if (this.options.expandOnHash) {
            window.addEventListener('hashchange', this.handleHashChange.bind(this));
            this.handleHashChange();
        }
    }

    updateConfig(newConfig) {
        this.options = { ...this.options, ...newConfig };
    }

    toggle() {
        if (this.isExpanded || this.isExpanding) {
            if (!this.options.allowClose) {
                return;
            }

            this.collapse();
        } else if (!this.isExpanded || this.isClosing) {
            this.expand();
        }
    }

    collapse() {
        this.el.dispatchEvent(Disclosure.events.collapsebegin);
        this.isClosing = true;
        this.isExpanded = false;

        // If there is already an animation running, cancel it
        if (this.options.animate && this.animation) {
            this.animation.cancel();
        }

        this.el.setAttribute('aria-hidden', 'true');

        this.options.setInertAttribute && this.el.setAttribute('inert', true);

        this.triggerElements.forEach((trigger) => trigger.setAttribute('aria-expanded', 'false'));

        if (this.options.updateChildTabIndexes === true) {
            this.focusableItems.hideAllFromKeyboard();
        }

        this.toggleLinkedElements();

        if (this.options.animate) {
            this.el.style.overflow = 'hidden';

            const startHeight = `${this.el.offsetHeight}px`;
            const endHeight = `0px`;

            // Start a WAAPI animation
            this.animation = this.el.animate(
                {
                    // Set the keyframes from the startHeight to endHeight
                    height: [startHeight, endHeight],
                },
                {
                    duration: this.options.animateDuration,
                    easing: this.options.animateEasing,
                }
            );

            this.animation.onfinish = () => this.completeCollapse();
            this.animation.oncancel = () => (this.isClosing = false);
        } else {
            this.completeCollapse();
        }
    }

    completeCollapse() {
        this.isClosing = false;

        if (this.options.animate) {
            this.animation = null;

            // Remove the overflow hidden and the fixed height
            this.el.style.height = this.el.style.overflow = '';
        }

        if (this.options.setHiddenAttribute === true) {
            this.el.setAttribute('hidden', 'hidden');
        }

        this.el.dispatchEvent(Disclosure.events.collapseend);
    }

    expand() {
        this.el.dispatchEvent(Disclosure.events.expandbegin);
        this.isExpanding = true;

        this.toggleLinkedElements();

        // If there is already an animation running, cancel it
        if (this.animation) {
            this.animation.cancel();
        }

        this.triggerElements.forEach((trigger) => trigger.setAttribute('aria-expanded', 'true'));

        if (this.options.updateChildTabIndexes === true) {
            this.focusableItems.resetTabIndex();
        }

        this.el.removeAttribute('aria-hidden');

        this.options.setInertAttribute && this.el.removeAttribute('inert');

        if (this.options.setHiddenAttribute === true) {
            this.el.removeAttribute('hidden');
        }

        if (this.options.animate) {
            this.el.style.overflow = 'hidden';

            const startHeight = `0px`;
            const endHeight = `${this.el.scrollHeight}px`;

            this.animation = this.el.animate(
                {
                    height: [startHeight, endHeight],
                },
                {
                    duration: this.options.animateDuration,
                    easing: this.options.animateEasing,
                }
            );

            this.animation.onfinish = () => this.completeExpand();
            this.animation.oncancel = () => (this.isExpanding = false);
        } else {
            this.completeExpand();
        }
    }

    completeExpand() {
        this.isExpanding = false;
        this.isExpanded = true;

        if (this.options.animate) {
            this.animation = null;

            // Remove the overflow hidden and the fixed height
            this.el.style.height = this.el.style.overflow = '';
        }

        this.el.dispatchEvent(Disclosure.events.expandend);
    }

    toggleLinkedElements() {
        const toggleVisibility = (selector, shouldShow) => {
            document.querySelectorAll(selector).forEach((element) => {
                element.toggleAttribute('hidden', !shouldShow);
            });
        };

        toggleVisibility(`[data-hide-expanded="${this.el.id}"]`, !this.isExpanding);
        toggleVisibility(`[data-show-expanded="${this.el.id}"]`, this.isExpanding);
    }

    /**
     * Sets the Disclosure's required elements - a target and at least one trigger - returning success/failure.
     *
     * @returns {boolean} Whether setting up the required elements was successful.
     */
    setUpElements() {
        // Bail early - invalid target element passed.
        if (!this.el || !(this.el instanceof HTMLElement)) {
            console.error('Invalid target element', this.el, this);
            return false;
        }

        if (!this.el.hasAttribute('id') || this.el.id === '') {
            console.error('Target element missing required "id" attribute', this.el, this);
            return false;
        }

        this.triggerElements = document.querySelectorAll(`[aria-controls=${this.el.id}]`);

        // Bail early - invalid trigger element passed.
        if (!this.triggerElements || this.triggerElements.length < 1) {
            console.error('No trigger elements found', this.triggerElements, this);
            return false;
        }

        this.triggerElements.forEach((trigger) => {
            // Improve accessibility of trigger element if it isn't a <button>.
            if (trigger.tagName !== 'BUTTON') {
                trigger.setAttribute('role', 'button');
            }

            // Ensure required accessibility attribute is set.
            trigger.setAttribute('aria-expanded', this.isExpanded ? 'true' : 'false');
        });

        if (!this.isExpanded) {
            this.options.setInertAttribute && this.el.setAttribute('inert', true);
        }

        return true;
    }

    handlePotentialFocusLoss(event) {
        if (!event.relatedTarget) {
            return;
        }

        if (this.el.contains(event.relatedTarget)) {
            return;
        }

        if ([...this.triggerElements].includes(event.relatedTarget)) {
            return;
        }

        this.collapse();
    }

    /**
     * Handle events with class functions to retain class context.
     *
     * @link https://webreflection.medium.com/dom-handleevent-a-cross-platform-standard-since-year-2000-5bf17287fd38
     *
     * @param {Event} event An event object.
     */
    handleEvent(event) {
        this[`on${event.type}`](event);

        // add event listeners from 'on' options
        Object.keys(this.options.on).forEach((eventName) => {
            if (eventName === event.type) {
                this.options.on[eventName](event);
            }
        });
    }

    onclick(event) {
        if (
            [...this.triggerElements].includes(event.target) ||
            [...this.triggerElements].some((trigger) => trigger.contains(event.target))
        ) {
            this.lastTrigger = event.target;
            this.toggle();
        } else if (
            this.options.collapseOnFocusout === true &&
            this.isExpanded &&
            this.options.allowClose === true &&
            !this.el.contains(event.target)
        ) {
            this.collapse();
        }
    }

    onfocusout(event) {
        this.handlePotentialFocusLoss(event);
    }

    onblur(event) {
        this.handlePotentialFocusLoss(event);
    }

    onkeydown(event) {
        if (event.key !== 'Escape') {
            return;
        }

        if (!this.el.contains(event.target)) {
            return;
        }

        this.collapse();

        // Replace focus for keydown events.
        if (this.lastTrigger) {
            this.lastTrigger.focus();
            this.lastTrigger = null;
        }

        // Conditionally prevent ancestor elements from collapsing.
        if (this.options.collapseAncestorsOnEscape === false) {
            event.stopPropagation();
        }
    }

    oncollapsebegin() {
        this.el.removeEventListener('collapsebegin', this);

        if (this.options.collapseOnFocusout === true) {
            this.el.removeEventListener('focusout', this);
            this.triggerElements.forEach((trigger) => trigger.removeEventListener('blur', this));
        }

        if (this.options.collapseOnEscape === true) {
            this.el.removeEventListener('keydown', this);
        }

        if (this.options.collapseOnAncestorCollapse === true) {
            document.removeEventListener('collapseend', this);
        }
    }

    oncollapseend({ target }) {
        if (target === this.el) {
            // Stop listening for the Disclosure collapse event
            this.el.removeEventListener('collapseend', this);

            // Listen for Disclosure expand events
            this.el.addEventListener('expandbegin', this);
            this.el.addEventListener('expandend', this);
        } else if (this.options.collapseOnAncestorCollapse === true && target.contains(this.el)) {
            this.collapse();
        }
    }

    onexpandbegin() {
        this.el.removeEventListener('expandbegin', this);
    }

    onexpandend() {
        // Stop handling Disclosure expand.
        this.el.removeEventListener('expandend', this);

        // Start handling Disclosure collapse.
        this.el.addEventListener('collapsebegin', this);
        this.el.addEventListener('collapseend', this);

        if (this.options.focusWithinOnExpand === true) {
            window.setTimeout(() => {
                // If the parent element was display:none, focus must be set after the parent element displays.
                console.log('attempting focus within disclosure', this.focusableItems);
                this.focusableItems.firstFocusable.focus();
            }, 100);
        }

        if (this.options.collapseOnFocusout === true) {
            this.el.addEventListener('focusout', this);
            this.triggerElements.forEach((trigger) => trigger.addEventListener('blur', this));
        }

        if (this.options.collapseOnEscape === true) {
            this.el.addEventListener('keydown', this);
        }

        if (this.options.collapseOnAncestorCollapse === true) {
            document.addEventListener('collapseend', this);
        }
    }

    handleGlobalExpand(event) {
        if (!this.isExpanded) return;

        let shouldClose = false;

        if (this.options.collapseOnOtherElementsExpand) {
            switch (typeof this.options.collapseOnOtherElementsExpand) {
                case 'object':
                    // A nodeList was passed
                    this.options.collapseOnOtherElementsExpand.forEach((element) => {
                        if (event.target === element) {
                            shouldClose = true;
                        }
                    });

                    break;
            }
        }

        if (this.options.disclosureGroup) {
            console.log(event.target.dataset.disclosureGroup);

            if (event.target.dataset.disclosureGroup === this.options.disclosureGroup) {
                shouldClose = true;
            }
        }

        if (shouldClose) {
            this.collapse();
        }
    }

    handleHashChange() {
        if (window.location.hash === `#${this.el.id}`) {
            this.expand();
        }
    }

    getReducedMotion() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return true;
        }

        const stored = localStorage.getItem('accessibilitySettingsV1');

        if (stored) {
            const settings = JSON.parse(stored);

            return settings.reducedMotion;
        }
    }

    /**
     * Helper method to properly handle boolean data attributes
     * Returns true if the data attribute exists (regardless of value), unless explicitly set to 'false'
     * @param {string} attributeName - The data attribute name (without 'data-' prefix)
     * @param {boolean} defaultValue - The default value to return if attribute doesn't exist
     * @returns {boolean}
     */
    getBooleanDataAttribute(attributeName, defaultValue = false) {
        const attributeValue = this.el.dataset[attributeName];

        // If the attribute doesn't exist, return the default value
        if (attributeValue === undefined) {
            return defaultValue;
        }

        // If the attribute exists but is explicitly set to 'false', return false
        if (attributeValue === 'false') {
            return false;
        }

        // If the attribute exists (even with empty string or any other value), return true
        return true;
    }

    // Custom events for listeners.
    static events = {
        get expandbegin() {
            return new Event('expandbegin', { bubbles: true });
        },
        get expandend() {
            return new Event('expandend', { bubbles: true });
        },
        get collapsebegin() {
            return new Event('collapsebegin', { bubbles: true });
        },
        get collapseend() {
            return new Event('collapseend', { bubbles: true });
        },
    };
}

// Instantiate the class for all [data-disclosure] elements
dynamicElements.define('[data-disclosure]', (el) => new Disclosure(el));

// Export the events for external listeners.
export const { events } = Disclosure;
