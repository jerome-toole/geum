import dynamicElements from './dynamicElements.js';

/**
 * Dialog utility to enable more accessible and feature-rich dialogs.
 * This class is a work in progress.
 */
export default class Dialog {
    constructor(element, options = {}) {
        this.el = element;
        this.options = {
            modal: this.el.getAttribute('aria-modal') === 'true',
            allowBodyScroll: this.getBooleanDataAttribute('dialogAllowBodyScroll', false),
            backdropClose: true,
            closeTransitionDuration: this.el.dataset.dialogCloseTransitionDuration ?? 450,
            ...options,
        };

        this.isOpen = false;
        this.name = this.options.name ?? this.el.getAttribute('data-dialog');
        this.type = this.el.dataset.dialogType;
        this.dialogOpenElements = document.querySelectorAll(`[data-dialog-open='${this.name}']`);
        this.dialogCloseElements = document.querySelectorAll(`[data-dialog-close='${this.name}']`);
        this.dialogCloseElements = Array.from(this.dialogCloseElements).concat(
            Array.from(this.el.querySelectorAll('[data-dialog-close]'))
        );

        this.contentSlot = this.el.querySelector('[data-dialog-content]');
        this.content = document.getElementById(this.el.dataset.dialogContentId);

        this.init();
    }

    init() {
        this.dialogOpenElements.forEach((element) => {
            element.addEventListener('click', (e) => {
                if (element.dataset.dialogContentTemplate) {
                    this.content = document.querySelector(
                        `[data-dialog-content='${element.dataset.dialogContentTemplate}']`
                    );
                }

                this.open(e);
            });
        });

        this.dialogCloseElements.forEach((element) => {
            element.addEventListener('click', (e) => {
                this.close(e);
            });
        });

        // Optionally close dialog when clicking on backdrop
        this.el.addEventListener('click', (e) => {
            if (this.options.backdropClose && e.target === this.el) {
                this.close(e);
            }
        });

        // Handle the 'close' event
        this.el.addEventListener('close', () => {
            this.onClose();
        });

        this.setScrollbarWidth();
    }

    open(e) {
        if (this.options.modal) {
            if (!this.options.allowBodyScroll) this.fixBody();

            this.el.showModal();
        } else {
            this.el.show();
        }

        document.body.classList.add('dialog-is-open');
        document.body.classList.add(`${this.name}-dialog-is-open`);

        this.el.setAttribute('aria-hidden', 'false');

        // If a content is specified, inject the content into the dialog's slot
        if (this.contentSlot && this.content) {
            this.contentSlot.innerHTML = this.content.innerHTML;
        }

        // If a dialog type has been set, add it as a data attribute
        if (this.type) {
            this.el.setAttribute('data-type', type);
        }
    }

    close(e) {
        this.el.close();
    }

    onClose() {
        if (this.contentSlot) {
            setTimeout(() => {
                if (this.el.open) return; // Ensure dialog hasn't been opened again

                this.contentSlot.innerHTML = '';
            }, this.options.closeTransitionDuration);
        }

        this.unfixBody();

        document.body.classList.remove('dialog-is-open');
        document.body.classList.remove(`${this.name}-dialog-is-open`);

        this.el.setAttribute('aria-hidden', 'true');
        this.el.setAttribute('data-type', '');
    }

    fixBody() {
        document.documentElement.classList.add('no-scroll');
    }

    unfixBody() {
        document.documentElement.classList.remove('no-scroll');
    }

    setScrollbarWidth() {
        // Use this to stop elements jumping when dialog is opened
        document.documentElement.style.setProperty(
            '--scrollbar-width',
            window.innerWidth - document.documentElement.clientWidth + 'px'
        );
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
}

dynamicElements.define('[data-dialog]', (el) => new Dialog(el));
