/**
 * PostCSS Color System Plugin
 * Generates CSS custom properties from color configuration
 */

import { readFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = dirname(fileURLToPath(import.meta.url));

// Load colors from central config
const themeConfig = JSON.parse(
	readFileSync(resolve(__dirname, '../assets/theme-config.json'), 'utf-8')
);
const colors = themeConfig.colors;

/**
 * Convert hex to HSL
 */
function hexToHSL(hex) {
	// Remove the hash if present
	hex = hex.replace('#', '');

	// Expand shorthand hex (e.g., "fff" → "ffffff")
	if (hex.length === 3) {
		hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
	}

	// Parse r, g, b values
	const r = parseInt(hex.substring(0, 2), 16) / 255;
	const g = parseInt(hex.substring(2, 4), 16) / 255;
	const b = parseInt(hex.substring(4, 6), 16) / 255;

	const max = Math.max(r, g, b);
	const min = Math.min(r, g, b);
	let h,
		s,
		l = (max + min) / 2;

	if (max === min) {
		h = s = 0; // achromatic (grayscale)
	} else {
		const d = max - min;
		s = l > 0.5 ? d / (2 - max - min) : d / (max + min);

		switch (max) {
			case r:
				h = ((g - b) / d + (g < b ? 6 : 0)) / 6;
				break;
			case g:
				h = ((b - r) / d + 2) / 6;
				break;
			case b:
				h = ((r - g) / d + 4) / 6;
				break;
		}
	}

	h = Math.round(h * 360);
	s = Math.round(s * 100);
	l = Math.round(l * 100);

	return `${h} ${s}% ${l}%`;
}

/**
 * Resolve named color references
 */
function resolveColor(colorName, theme = 'base') {
	const themeColors = colors[theme];
	if (!themeColors || !themeColors[colorName]) {
		return null;
	}

	const colorMap = themeColors[colorName];

	// If it references another color, resolve recursively
	if (colorMap.namedColor) {
		return resolveColor(colorMap.namedColor, 'base');
	}

	return colorMap.color;
}

/**
 * Resolve full color config (follows namedColor references)
 */
function resolveColorConfig(colorName, theme = 'base') {
	const themeColors = colors[theme];
	if (!themeColors || !themeColors[colorName]) {
		return null;
	}

	const colorMap = themeColors[colorName];

	if (colorMap.namedColor) {
		return resolveColorConfig(colorMap.namedColor, 'base');
	}

	return colorMap;
}

/**
 * Get foreground color value for a named color
 * @param {string} colorName - Color name (e.g., 'brand-1', 'darkgreen')
 * @returns {string|null} - Foreground color value or null
 */
export function getForegroundColor(colorName) {
	const config = resolveColorConfig(colorName);
	return config?.foreground || null;
}

/**
 * PostCSS plugin
 */
export default function postcssColorSystem() {
	return {
		postcssPlugin: 'postcss-color-system',
		Once(root, { Rule, Declaration, AtRule }) {
			// Generate @source inline for Tailwind v4 JIT to include dynamic classes
			const colorNames = Object.keys(colors.base);
			const dynamicClasses = colorNames.flatMap((name) => [
				`has-${name}-background-color`,
				`color-context-${name}`,
				`foreground-from-${name}`,
			]);
			const sourceRule = new AtRule({
				name: 'source',
				params: `inline("${dynamicClasses.join(' ')}")`,
			});
			root.prepend(sourceRule);

			// Inject colors into @theme for Tailwind utilities
			root.walkAtRules('theme', (atRule) => {
				Object.entries(colors.base).forEach(([colorName]) => {
					const prop = `--color-${colorName}`;

					// Check if already exists
					let exists = false;
					atRule.walkDecls(prop, () => {
						exists = true;
					});

					if (!exists) {
						atRule.append(
							new Declaration({
								prop: prop,
								value: `var(--color-${colorName})`,
							}),
						);
					}
				});
			});

			// Generate color variables for each theme
			Object.entries(colors).forEach(([themeName, themeColors]) => {
				const selector = themeName === 'base' ? ':root' : `:root.theme--${themeName}`;

				// Find existing :root rule or create new one
				let targetRule = null;
				if (themeName === 'base') {
					root.walkRules(':root', (rule) => {
						if (!targetRule) targetRule = rule;
					});
				}

				if (!targetRule) {
					targetRule = new Rule({ selector });
				}

				// Generate custom properties for each color
				Object.entries(themeColors).forEach(([colorName, colorMap]) => {
					const propName = `--color-${colorName}`;

					// Resolve the actual color value
					const colorValue = resolveColor(colorName, themeName);

					if (colorValue) {
						// Add main color property
						targetRule.append(
							new Declaration({
								prop: propName,
								value: colorValue,
							}),
						);

						// Add HSL variant
						const hslValue = hexToHSL(colorValue);
						targetRule.append(
							new Declaration({
								prop: `${propName}--hsl`,
								value: hslValue,
							}),
						);

						// Add individual H, S, L components
						const [h, s, l] = hslValue.split(' ');
						targetRule.append(new Declaration({ prop: `${propName}--h`, value: h }));
						targetRule.append(new Declaration({ prop: `${propName}--s`, value: s }));
						targetRule.append(new Declaration({ prop: `${propName}--l`, value: l }));
					}

					// Add foreground color if specified
					if (colorMap.foreground) {
						targetRule.append(
							new Declaration({
								prop: `${propName}--foreground`,
								value: colorMap.foreground,
							}),
						);
					}

					// Add additional properties if specified
					if (colorMap.properties) {
						Object.entries(colorMap.properties).forEach(([prop, value]) => {
							targetRule.append(new Declaration({ prop, value }));
						});
					}
				});

				// Append the rule if it was newly created
				if (!targetRule.parent) {
					root.append(targetRule);
				}
			});

			// Generate utility classes using @utility syntax for Tailwind v4
			const baseColors = colors.base;

			// Helper function to create background context utilities
			const createBackgroundContextUtility = (className, colorName, colorMap) => {
				const utilityRule = new AtRule({
					name: 'utility',
					params: className
				});

				// Set background color via custom property
				if (colorName !== 'background') {
					utilityRule.append(
						new Declaration({
							prop: '--color-background',
							value: `var(--color-${colorName})`,
						}),
					);
				}

				utilityRule.append(
					new Declaration({
						prop: 'background-color',
						value: 'var(--color-background)',
					}),
				);

				// Set foreground color if defined
				if (colorMap.foreground) {
					utilityRule.append(
						new Declaration({
							prop: '--color-foreground',
							value: colorMap.foreground,
						}),
					);
					utilityRule.append(
						new Declaration({
							prop: '--focus--color',
							value: 'var(--color-foreground)',
						}),
					);
					utilityRule.append(
						new Declaration({
							prop: 'color',
							value: 'var(--color-foreground)',
						}),
					);
				}

				// Set additional properties (link colors, etc.)
				if (colorMap.properties) {
					Object.entries(colorMap.properties).forEach(([prop, value]) => {
						utilityRule.append(new Declaration({ prop, value }));
					});
				}

				return utilityRule;
			};

			Object.entries(baseColors).forEach(([colorName, colorMap]) => {
				// Resolve the actual color data (handles namedColor references)
				const resolvedColor = resolveColor(colorName, 'base');
				const actualColorMap = colorMap.namedColor ? colors.base[colorMap.namedColor] : colorMap;

				// Skip if we can't resolve the color
				if (!resolvedColor || !actualColorMap) return;

				// Generate color-context-{color} utilities
				const contextUtilityRule = createBackgroundContextUtility(`color-context-${colorName}`, colorName, actualColorMap);
				root.append(contextUtilityRule);

				// Generate has-{color}-background-color utilities (WordPress alias)
				const hasUtilityRule = createBackgroundContextUtility(`has-${colorName}-background-color`, colorName, actualColorMap);
				root.append(hasUtilityRule);

				// Generate foreground-from-{color} utilities
				if (actualColorMap.foreground) {
					const foregroundUtilityRule = new AtRule({
						name: 'utility',
						params: `foreground-from-${colorName}`
					});

					foregroundUtilityRule.append(
						new Declaration({
							prop: '--color-foreground',
							value: actualColorMap.foreground,
						}),
					);
					foregroundUtilityRule.append(
						new Declaration({
							prop: '--focus--color',
							value: 'var(--color-foreground)',
						}),
					);
					foregroundUtilityRule.append(
						new Declaration({
							prop: 'color',
							value: 'var(--color-foreground)',
						}),
					);

					// Set additional properties (link colors, etc.) for foreground utilities
					if (actualColorMap.properties) {
						Object.entries(actualColorMap.properties).forEach(([prop, value]) => {
							foregroundUtilityRule.append(new Declaration({ prop, value }));
						});
					}

					root.append(foregroundUtilityRule);
				}
			});
		},
	};
}

postcssColorSystem.postcss = true;
