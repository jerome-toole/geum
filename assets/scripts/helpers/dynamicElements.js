/**
 * dynamicElements - A lightweight utility for automatic initialization of elements.
 *
 * This utility works similarly to `customElements.define()`, but doesn't require custom element markup.
 * It listens for `DOMContentLoaded` and `MutationObserver` events to initialize elements as they appear in the DOM.
 *
 * Usage:
 * ------
 * 1. Import `dynamicElements` into your module.
 * 2. Call `dynamicElements.define(selector, callback)`, where:
 *    - `selector` is a CSS selector for the elements you want to observe.
 *    - `callback` is a function that runs when an element is found.
 *
 * Example:
 * --------
 * import dynamicElements from "./dynamicElements.js";
 * import Disclosure from "./Disclosure.js";
 *
 * dynamicElements.define("[data-disclosure]", el => new Disclosure(el));
 *
 * This ensures that:
 * - Any `[data-tooltip]` elements already in the DOM are initialized on page load.
 * - Any new `[data-tooltip]` elements added later are also automatically initialized.
 *
 * The observer runs continuously and prevents duplicate initializations.
 *
 * @typedef {Object} DynamicElementOptions
 * @property {boolean} [loadOnReady=true] - Whether to initialize elements on DOMContentLoaded
 * @property {boolean} [watch=true] - Whether to watch for new elements using MutationObserver
 */

const dynamicElements = {
    registry: new Map(),
    observer: new MutationObserver((mutations) =>
        dynamicElements.handleMutations(mutations),
    ),
    isObserving: false,

    /**
     * Define a new dynamic element
     * @param {string} selector - CSS selector for the elements to observe
     * @param {Function} callback - Function to run when an element is found
     * @param {DynamicElementOptions} [options={ loadOnReady: true, watch: true }] - Configuration options
     */
    define(selector, callback, options = {}) {
        const { loadOnReady = true, watch = true } = options;

        this.registry.set(selector, { callback, watch });

        if (loadOnReady) {
            this.initOnDOMContentLoaded(selector, callback);
        }

        if (watch && !this.isObserving) {
            this.observe();
        }
    },

    observe() {
        this.observer.observe(document.documentElement, {
            childList: true,
            subtree: true,
        });

        this.isObserving = true;
    },

    /**
     * Initializes existing elements, but waits for DOMContentLoaded if necessary
     */
    initOnDOMContentLoaded(selector, callback) {
        if (document.readyState === "loading") {
            // If the document is still loading, wait for DOMContentLoaded
            document.addEventListener("DOMContentLoaded", () =>
                this.initExistingElements(selector, callback),
            );
        } else {
            // If the document is already loaded, initialize immediately
            this.initExistingElements(selector, callback);
        }
    },

    initExistingElements(selector, callback) {
        document
            .querySelectorAll(selector)
            .forEach((el) => this.initElement(el, callback));
    },

    handleMutations(mutations) {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === 1) {
                    this.registry.forEach(({ callback, watch }, selector) => {
                        if (!watch) return;

                        if (node.matches(selector)) {
                            this.initElement(node, callback);
                        } else {
                            node.querySelectorAll(selector).forEach((el) =>
                                this.initElement(el, callback),
                            );
                        }
                    });
                }
            });
        });
    },

    initElement(el, callback) {
        if (!el.__initialized) {
            callback(el);
            el.__initialized = true; // Prevent duplicate initialization
        }
    },
};

export default dynamicElements;
