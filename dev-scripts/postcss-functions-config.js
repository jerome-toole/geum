/**
 * PostCSS Functions Configuration
 */

import { getForegroundColor } from './postcss-color-system.js';

export default {
	functions: {
		/**
		 * Strip unit from a value
		 * @param {string} value - The value with unit (e.g., "16px", "1rem")
		 * @returns {number} - The numeric value without unit
		 */
		'strip-unit': (value) => {
			const parsed = parseFloat(value);
			return isNaN(parsed) ? value : parsed;
		},

		/**
		 * Space - convert px value to rem (or px with second argument)
		 * @param {string|number} value - Size in px
		 * @param {string} unit - Optional unit: 'px' for pixels, otherwise rem (default)
		 * @returns {string} - rem or px value
		 * @example space(16) → '1rem'
		 * @example space(16, 'px') → '16px'
		 */
		space: (value, unit = 'rem') => {
			const stripUnit = (val) => parseFloat(val);
			const numValue = stripUnit(value);
			if (unit === 'px') {
				return `${numValue}px `;
			}
			return `${numValue / 16}rem `;
		},

		/**
		 * Space Fluid - responsive spacing that scales between min and max
		 * @param {string|number} min - Minimum size in px (or single value for static output)
		 * @param {string|number} max - Maximum size in px (optional)
		 * @returns {string} - calc() expression using --fluid-bp, or rem value if single arg
		 * @example spaceFluid(12, 16) → 'calc(((12 / 16) * 1rem) + (16 - 12) * var(--fluid-bp))'
		 * @example spaceFluid(16) → '1rem'
		 */
		spaceFluid: (min, max) => {
			const stripUnit = (val) => parseFloat(val);
			const minVal = stripUnit(min);

			// If only one argument, output rem value
			if (max === undefined) {
				return `${minVal / 16}rem`;
			}

			const maxVal = stripUnit(max);
			return `calc(((${minVal} / 16) * 1rem) + (${maxVal} - ${minVal}) * var(--fluid-bp))`;
		},

		/**
		 * Convert px to rem
		 * @param {string} target - Target size in px
		 * @param {string|number} base - Base font size (default: 16)
		 * @returns {string} - rem value
		 */
		rem: (target, base = 16) => {
			const stripUnit = (val) => parseFloat(val);
			const targetVal = stripUnit(target);
			const baseVal = stripUnit(base);
			const size = targetVal / baseVal;
			return `${size}rem`;
		},

		/**
		 * Convert px to em
		 * @param {string} target - Target size in px
		 * @param {string|number} base - Base font size (default: 16)
		 * @returns {string} - em value
		 */
		em: (target, base = 16) => {
			const stripUnit = (val) => parseFloat(val);
			const targetVal = stripUnit(target);
			const baseVal = stripUnit(base);
			const size = targetVal / baseVal;
			return `${size}em`;
		},

		/**
		 * Responsive unit - fluidly increases from min to max between fluid breakpoints
		 * @param {number} min - Minimum value
		 * @param {number} max - Maximum value
		 * @param {string} unit - Unit to use (default: '1rem')
		 * @returns {string} - calc() expression
		 */
		ru: (min, max, unit = '1rem') => {
			const stripUnit = (val) => parseFloat(val);
			const minVal = stripUnit(min);
			const maxVal = stripUnit(max);
			return `calc(((${minVal} / 16) * ${unit}) + (${maxVal} - ${minVal}) * var(--fluid-bp))`;
		},

		/**
		 * Responsive font-size - alias for ru() with 1rem unit
		 * @param {number} min - Minimum font size
		 * @param {number} max - Maximum font size
		 * @returns {string} - calc() expression
		 */
		rfs: (min, max) => {
			const stripUnit = (val) => parseFloat(val);
			const minVal = stripUnit(min);
			const maxVal = stripUnit(max);
			return `calc(((${minVal} / 16) * 1rem) + (${maxVal} - ${minVal}) * var(--fluid-bp))`;
		},

		/**
		 * Generate transition for multiple properties
		 * @param {...string} properties - CSS properties to transition
		 * @returns {string} - Comma-separated transition values
		 */
		transition: (...properties) => {
			return properties
				.map((prop) => `${prop} var(--transition--duration) var(--transition--ease)`)
				.join(', ');
		},

		/**
		 * Get foreground color for a named color
		 * @param {string} colorName - Color name (e.g., 'brand-1', 'darkgreen')
		 * @returns {string} - Foreground color value
		 * @example foreground-color('brand-1') → 'var(--color-white)'
		 */
		'foreground-color': (colorName) => {
			const name = colorName.replace(/['"]/g, '');
			const value = getForegroundColor(name);
			if (!value) {
				console.warn(`[foreground-color] No foreground defined for "${name}"`);
				return 'inherit';
			}
			return value;
		},
	},
};
