import { dirname, relative, resolve } from 'node:path';
import fg from 'fast-glob';

/**
 * PostCSS plugin to handle glob imports in CSS like:
 * @import './components/** /styles-main.pcss';
 * Must run BEFORE postcss-import
 */
export default function postcssGlobImport(_opts = {}) {
    return {
        postcssPlugin: 'postcss-glob-import',

        prepare(result) {
            const inputFile = result.opts.from;

            return {
                AtRule: {
                    import: (atRule) => {
                        if (!inputFile) return;

                        const inputDir = dirname(inputFile);
                        const importPath = atRule.params.replace(/['"]/g, '').trim();

                        // Check if it's a glob pattern
                        if (!importPath.includes('**')) return;

                        // Resolve glob pattern relative to the current file
                        const fullPattern = resolve(inputDir, importPath);

                        // Find matching files
                        const files = fg.sync(fullPattern);

                        if (files.length === 0) {
                            console.warn(`No files found for glob pattern: ${importPath}`);
                            atRule.remove();
                            return;
                        }

                        // Generate imports
                        const newImports = files.map((file) => {
                            const relativePath = relative(inputDir, file);
                            const normalizedPath = relativePath.replace(/\\/g, '/');
                            // Ensure it starts with ./
                            const finalPath = normalizedPath.startsWith('.') ? normalizedPath : `./${normalizedPath}`;
                            return `'${finalPath}'`;
                        });

                        // Replace with the first import
                        atRule.params = newImports[0];

                        // Add remaining imports after this one
                        for (let i = 1; i < newImports.length; i++) {
                            const newAtRule = atRule.clone({ params: newImports[i] });
                            atRule.parent.insertAfter(atRule, newAtRule);
                        }
                    },
                },
            };
        },
    };
}

postcssGlobImport.postcss = true;
