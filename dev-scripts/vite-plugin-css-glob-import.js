import { dirname, resolve } from 'node:path';
import fg from 'fast-glob';

/**
 * Vite plugin to handle glob imports in CSS like:
 * @import './components/** /styles-main.pcss';
 */
export default function cssGlobImportPlugin() {
    return {
        name: 'vite-plugin-css-glob-import',

        transform(code, id) {
            // Only process CSS files
            if (!/\.pcss$/.test(id)) return null;

            // Look for glob import patterns
            const globImportRegex = /@import\s+['"]([^'"]+\/\*\*\/[^'"]+)['"]/g;

            let match;
            const matches = [];

            while ((match = globImportRegex.exec(code)) !== null) {
                matches.push(match);
            }

            if (matches.length === 0) return null;

            let transformedCode = code;

            // Process each glob import
            for (const match of matches) {
                const globPattern = match[1];
                const fullPattern = resolve(dirname(id), globPattern);

                // Find matching files
                const files = fg.sync(fullPattern);

                // Generate individual imports
                const imports = files
                    .map((file) => {
                        const relativePath = `./${file.replace(`${dirname(id)}/`, '')}`;
                        return `@import '${relativePath}';`;
                    })
                    .join('\n');

                // Replace the glob import with individual imports
                transformedCode = transformedCode.replace(match[0], imports);
            }

            return {
                code: transformedCode,
                map: null,
            };
        },
    };
}
