import { readFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import hoverVariant from './dev-scripts/tailwind-hover-variant.js';

const __dirname = dirname(fileURLToPath(import.meta.url));

// Load colors from central config
const themeConfig = JSON.parse(readFileSync(resolve(__dirname, 'assets/theme-config.json'), 'utf-8'));

// Build Tailwind color map from config
function buildTailwindColors(colorConfig) {
    const result = {};
    const baseColors = colorConfig.colors?.base || {};

    for (const [name] of Object.entries(baseColors)) {
        result[name] = `var(--color-${name})`;
    }

    return result;
}

/** @type {import('tailwindcss').Config} */
export default {
    content: ['./assets/**/*.{js,css,pcss}', './**/*.php', '!./vendor/**', '!./node_modules/**'],
    safelist: buildSafelist(themeConfig),
    theme: {
        extend: {
            colors: buildTailwindColors(themeConfig),
        },
    },
    plugins: [hoverVariant],
};
