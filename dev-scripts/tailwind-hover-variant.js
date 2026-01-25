import plugin from 'tailwindcss/plugin';

/**
 * Custom Tailwind plugin that provides a `hocus` variant
 * - On devices with hover capability: applies on :hover
 * - On touch devices: applies on :active
 */
export default plugin(function ({ addVariant }) {
    // The 'hocus' variant for use with utility classes
    addVariant('hocus', [
        '@media (hover: hover) { &:hover }',
        '@media (hover: none) { &:active }',
    ]);
});
