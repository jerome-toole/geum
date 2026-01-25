import Focusable from './Focusable.js';

/**
 * Expander
 *
 * Give a button element...
 * - [aria-expanded="BOOLEAN"]
 * - [aria-controls="TARGET ELEMENT ID"]
 *
 * Give the related expandable element...
 * - [id="TARGET ELEMENT ID"]
 *
 * a11y overview of aria-expanded: https://www.accessibility-developer-guide.com/examples/sensible-aria-usage/expanded/
 */
export default class Expander {
    constructor(element, options = {}) {
        // Bail early - required elements or markup not found.
        if (!this.setUpRequiredElements(element)) {
            return;
        }

        // Set instance options by combining default options with any overrides via spread syntax.
        this.options = {
            collapseOnEscape: true,
            collapseOnFocusout: false,
            collapseAncestorsOnEscape: false,
            collapseOnAncestorCollapse: true,
            focusWithinOnExpand: false,
            updateChildTabIndexes: true,
            setHiddenAttribute: true,
            on: {},
            ...options,
        };

        // Helper object to manage focusable elements inside expandable element.
        this.focusableItems = new Focusable(this.targetElement);

        // Toggle Expander on click.
        this.triggerElement.addEventListener('click', this);

        // Handle expanding.
        this.targetElement.addEventListener('expandbegin', this);
        this.targetElement.addEventListener('expandend', this);
    }

    updateConfig(newConfig) {
        this.options = { ...this.options, ...newConfig };
    }

    handlePotentialFocusLoss(event) {
        if (this.targetElement.contains(event.relatedTarget)) {
            return;
        }

        if (this.triggerElement === event.relatedTarget) {
            return;
        }

        this.collapse();
    }

    isExpanded() {
        return this.triggerElement.getAttribute('aria-expanded') === 'true';
    }

    expand() {
        this.targetElement.dispatchEvent(Expander.events.expandbegin);

        this.targetElement.removeAttribute('aria-hidden');

        if (this.options.setHiddenAttribute === true) {
            this.targetElement.removeAttribute('hidden');
        }

        this.triggerElement.setAttribute('aria-expanded', 'true');

        if (this.options.updateChildTabIndexes === true) {
            this.focusableItems.resetTabIndex();
        }

        this.targetElement.dispatchEvent(Expander.events.expandend);
    }

    collapse() {
        this.targetElement.dispatchEvent(Expander.events.collapsebegin);

        this.targetElement.setAttribute('aria-hidden', 'true');

        if (this.options.setHiddenAttribute === true) {
            this.targetElement.setAttribute('hidden', 'hidden');
        }

        this.triggerElement.setAttribute('aria-expanded', 'false');

        if (this.options.updateChildTabIndexes === true) {
            this.focusableItems.hideAllFromKeyboard();
        }

        this.targetElement.dispatchEvent(Expander.events.collapseend);
    }

    toggle() {
        if (this.isExpanded()) {
            this.collapse();
        } else {
            this.expand();
        }
    }

    /**
     * Sets the Expander's required elements - a trigger and a target - returning success/failure.
     *
     * @param {node} element The required triggering element.
     * @returns {boolean} Whether setting the required elements was successful.
     */
    setUpRequiredElements(element) {
        this.triggerElement = element;

        // Bail early - invalid trigger element passed.
        if (!(this.triggerElement && this.triggerElement instanceof HTMLElement)) {
            console.error('Invalid trigger element', this.triggerElement, this);
            return false;
        }

        // Bail early - trigger element not correctly linked to a target element.
        if (!this.triggerElement.hasAttribute('aria-controls')) {
            console.error('Trigger element missing required "aria-controls" attribute', this.triggerElement, this);
            return false;
        }

        this.targetElement = document.getElementById(this.triggerElement.getAttribute('aria-controls'));

        // Bail early - no valid target found.
        if (!this.targetElement) {
            console.error(
                `No target element found with ID: #${this.triggerElement.getAttribute('aria-controls')}`,
                this.triggerElement,
                this
            );
            return false;
        }

        // Improve accessibility of trigger element if it isn't a <button>.
        if (this.triggerElement.tagName !== 'BUTTON') {
            this.triggerElement.setAttribute('role', 'button');
        }

        return true;
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
        event.preventDefault();
        this.toggle();
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

        if (!this.targetElement.contains(event.target)) {
            return;
        }

        this.collapse();
        this.triggerElement.focus();

        // Conditionally prevent ancestor elements from collapsing.
        if (this.options.collapseAncestorsOnEscape === false) {
            event.stopPropagation();
        }
    }

    oncollapsebegin() {
        this.targetElement.removeEventListener('collapsebegin', this);

        if (this.options.collapseOnFocusout === true) {
            this.targetElement.removeEventListener('focusout', this);
            this.targetElement.removeEventListener('blur', this);
        }

        if (this.options.collapseOnEscape === true) {
            this.targetElement.removeEventListener('keydown', this);
        }

        if (this.options.collapseOnAncestorCollapse === true) {
            document.removeEventListener('collapseend', this);
        }
    }

    oncollapseend({ target }) {
        if (target === this.targetElement) {
            // Stop handling Expander collapse.
            this.targetElement.removeEventListener('collapseend', this);

            // Handle Expander expand.
            this.targetElement.addEventListener('expandbegin', this);
            this.targetElement.addEventListener('expandend', this);
        } else if (this.options.collapseOnAncestorCollapse === true && target.contains(this.targetElement)) {
            this.collapse();
        }
    }

    onexpandbegin() {
        this.targetElement.removeEventListener('expandbegin', this);
    }

    onexpandend() {
        // Stop handling Expander expand.
        this.targetElement.removeEventListener('expandend', this);

        // Start handling Expander collapse.
        this.targetElement.addEventListener('collapsebegin', this);
        this.targetElement.addEventListener('collapseend', this);

        if (this.options.focusWithinOnExpand === true) {
            window.setTimeout(() => {
                // If the parent element was display:none, focus must be set after the parent element displays.
                this.focusableItems.firstFocusable.focus();
            }, 100);
        }

        if (this.options.collapseOnFocusout === true) {
            this.targetElement.addEventListener('focusout', this);
            this.triggerElement.addEventListener('blur', this);
        }

        if (this.options.collapseOnEscape === true) {
            this.targetElement.addEventListener('keydown', this);
        }

        if (this.options.collapseOnAncestorCollapse === true) {
            document.addEventListener('collapseend', this);
        }
    }

    // Custom events for Expander state change listeners.
    static events = {
        get collapsebegin() {
            return new Event('collapsebegin');
        },
        get collapseend() {
            return new Event('collapseend', { bubbles: true });
        },
        get expandbegin() {
            return new Event('expandbegin');
        },
        get expandend() {
            return new Event('expandend', { bubbles: true });
        },
    };
}

// Class custom events - standardised for use elsewhere.
export const { events } = Expander;
