import { dirname, relative, resolve } from 'node:path';
import fg from 'fast-glob';

// Vite plugin to handle glob imports (e.g., ../components/*/scripts.js)
export default function globImportPlugin() {
    return {
        name: 'vite-plugin-glob-import',

        transform(code, id) {
            // Only process JS files
            if (!id.endsWith('.js')) return null;

            // Look for glob import patterns (single * or double **)
            const globImportRegex = /import\s+['"]([^'"]+\/\*+\/[^'"]+)['"]/g;

            let match;
            let transformedCode = code;
            const matches = [];

            while ((match = globImportRegex.exec(code)) !== null) {
                matches.push(match);
            }

            if (matches.length === 0) return null;

            const fileDir = dirname(id);

            // Process each glob import
            for (const match of matches) {
                const globPattern = match[1];
                const fullPattern = resolve(fileDir, globPattern);

                // Find matching files
                const files = fg.sync(fullPattern);

                // Generate individual imports with proper relative paths
                const imports = files
                    .map((file) => {
                        let relativePath = relative(fileDir, file);
                        if (!relativePath.startsWith('.')) {
                            relativePath = './' + relativePath;
                        }
                        return `import '${relativePath}';`;
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
