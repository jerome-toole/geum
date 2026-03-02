import { resolve } from 'node:path';
import fg from 'fast-glob';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import cssGlobImportPlugin from './dev-scripts/vite-plugin-css-glob-import.js';
import generateComponentImportsPlugin from './dev-scripts/vite-plugin-generate-component-imports.js';
import globImportPlugin from './dev-scripts/vite-plugin-glob-import.js';

// Discover standalone component files (named after component, e.g., button.js, button.pcss)
// Bundled files (styles.pcss, scripts.js) are imported via glob in main entry points
// Files starting with underscore are partials and should not be treated as standalone assets
const standaloneScripts = fg.sync('components/*/*.js').filter((file) => {
    const fileName = file.split('/').pop();
    return !(file.endsWith('/scripts.js') || file.endsWith('/editor-scripts.js') || fileName.startsWith('_'));
});

const standaloneStyles = fg.sync('components/*/*.pcss').filter((file) => {
    const fileName = file.split('/').pop();
    return !(file.endsWith('/styles.pcss') || fileName.startsWith('_'));
});

// Create entry points object
const input = {
    // Main entry points (these import component styles.pcss/scripts.js via glob)
    main: resolve(__dirname, 'assets/main.js'),
    'main-styles': resolve(__dirname, 'assets/main.pcss'),
    'editor-scripts': resolve(__dirname, 'assets/editor-scripts.js'),
    'editor-styles': resolve(__dirname, 'assets/editor-styles.pcss'),
    'admin-scripts': resolve(__dirname, 'assets/admin-scripts.js'),
};

// Add standalone component files as separate entry points
standaloneScripts.forEach((file) => {
    const name = file.replace('components/', 'components/').replace('.js', '');
    input[name] = resolve(__dirname, file);
});

standaloneStyles.forEach((file) => {
    const name = file.replace('components/', 'components/').replace('.pcss', '');
    input[name] = resolve(__dirname, file);
});

// Watches theme-config.json and forces CSS re-processing when it changes
function themeConfigWatcherPlugin() {
    const configPath = resolve(__dirname, 'assets/theme-config.json');
    return {
        name: 'theme-config-watcher',
        configureServer(server) {
            server.watcher.add(configPath);
            server.watcher.on('change', (file) => {
                if (file !== configPath) return;
                for (const mod of server.moduleGraph.idToModuleMap.values()) {
                    if (mod.id?.endsWith('.pcss') || mod.id?.endsWith('.css')) {
                        server.moduleGraph.invalidateModule(mod);
                    }
                }
                server.ws.send({ type: 'full-reload' });
            });
        },
    };
}

export default defineConfig({
    base: './',
    plugins: [
        themeConfigWatcherPlugin(),
        generateComponentImportsPlugin(),
        laravel({
            input: Object.values(input),
            publicDirectory: 'public',
            buildDirectory: 'build',
            hotFile: 'public/hot',
            refresh: ['**/*.php'],
        }),
        globImportPlugin(),
        cssGlobImportPlugin(),
    ],

    publicDir: 'assets/static',

    build: {
        sourcemap: process.env.NODE_ENV === 'development',
    },

    css: {
        postcss: './postcss.config.js',
    },
});
