#!/usr/bin/env node

import { readFileSync } from 'fs';
import wpPot from 'wp-pot';

// Read theme details from style.css
const styleCSS = readFileSync('./style.css', 'utf-8');
const themeName = styleCSS.match(/Theme Name:\s*(.+)/)?.[1] || 'geum';
const textDomain = styleCSS.match(/Text Domain:\s*(.+)/)?.[1] || 'geum';

wpPot({
    domain: textDomain,
    package: themeName,
    src: ['**/*.php', '!vendor/**', '!node_modules/**', '!build/**', '!tests/**'],
    destFile: `languages/${textDomain}.pot`,
    bugReport: 'https://github.com/your-repo/issues',
    team: 'Your Team <team@example.com>',
});

console.log(`✓ POT file generated: languages/${textDomain}.pot`);
