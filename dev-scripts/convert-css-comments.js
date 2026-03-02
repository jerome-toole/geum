import fg from 'fast-glob';
import { readFileSync, writeFileSync } from 'fs';

const files = fg.sync('**/*.pcss', { ignore: ['node_modules/**', 'public/**', 'vendor/**'] });

let updatedCount = 0;

for (const file of files) {
    const original = readFileSync(file, 'utf8');
    const updated = original
        // Standalone // comment lines (not preceded by :, which would be a URL like https://)
        .replace(/^(\s*)\/\/(.*)$/gm, '$1/*$2 */')
        // End-of-line // comments after semicolons
        .replace(/(;[ \t]*)\/\/(.*)$/gm, '$1/*$2 */');
    if (updated !== original) {
        writeFileSync(file, updated);
        console.log(`Updated: ${file}`);
        updatedCount++;
    }
}

console.log(`\nDone. Updated ${updatedCount} files.`);
