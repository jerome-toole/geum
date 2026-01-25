// See https://github.com/zellwk/javascript/blob/master/src/browser/accessibility/focusable.js
export default function Focusable(element = document) {
    return {
        get length() {
            return this.keyboardOnly.length;
        },

        get all() {
            return [
                ...element.querySelectorAll(`
                    a,
                    button,
                    input,
                    textarea,
                    select,
                    details,
                    iframe,
                    embed,
                    object,
                    summary dialog,
                    audio[controls],
                    video[controls],
                    [contenteditable],
                    [tabindex]
                `),
            ].filter((el) => {
                return (
                    el instanceof HTMLElement &&
                    !el.hasAttribute('disabled') &&
                    !el.hasAttribute('hidden') &&
                    el.style.display !== 'none'
                );
            });
        },

        get keyboardOnly() {
            return this.all.filter((el) => el.tabIndex > -1);
        },

        get firstFocusable() {
            return this.keyboardOnly[0];
        },

        get lastFocusable() {
            return this.keyboardOnly[this.length - 1];
        },

        set tabIndex(index) {
            this.all.forEach((el) => {
                el.tabIndex = index;
            });
        },

        hideAllFromKeyboard() {
            this.tabIndex = -1;
        },

        resetTabIndex() {
            this.all.forEach((el) => {
                el.removeAttribute('tabindex');
            });
        },
    };
}
