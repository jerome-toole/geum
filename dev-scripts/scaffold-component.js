#!/usr/bin/env node

/**
 * Component Scaffold
 *
 * Creates new component boilerplate.
 *
 * Usage:
 *   node dev-scripts/scaffold-component.js my-component
 *   node dev-scripts/scaffold-component.js my-component --styles --scripts --block
 *
 * Options:
 *   --styles    Create styles.pcss
 *   --scripts   Create scripts.js
 *   --block     Create acf.php for ACF block
 *   --all       Create all optional files
 */

import fs from 'fs';
import path from 'path';
import { execSync } from 'child_process';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const COMPONENTS_DIR = path.join(__dirname, '../components');

/**
 * Convert kebab-case to PascalCase
 */
function toPascalCase(str) {
    return str
        .split('-')
        .map(part => part.charAt(0).toUpperCase() + part.slice(1))
        .join('');
}

/**
 * Generate PHP class content
 */
function generateClass(name, className) {
    return `<?php

namespace Geum\\Components;

use Geum\\ComponentBase;

class ${className} extends ComponentBase
{
    protected static string $name = '${name}';

    public static function make(
        array $classes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function transform(array $args): array
    {
        $args['classes'] = array_merge(['${name}'], $args['classes'] ?? []);

        return $args;
    }
}
`;
}

/**
 * Generate template content
 */
function generateTemplate(name, className) {
    return `<?php
/**
 * ${className} Template
 *
 * @var \\Geum\\Components\\${className} $this
 */

use Geum\\Helpers;
?>

<div <?= Helpers::buildAttributes($this->attributes); ?>>
    <!-- ${className} content -->
</div>
`;
}

/**
 * Generate styles content
 */
function generateStyles(name) {
    return `.${name} {
    /* Component styles */
}
`;
}

/**
 * Generate scripts content
 */
function generateScripts(name) {
    return `const elements = document.querySelectorAll('.${name}');

elements.forEach((el) => {
    // Component logic
});
`;
}

/**
 * Generate ACF block content
 */
function generateAcfBlock(name, className) {
    const title = className.replace(/([A-Z])/g, ' $1').trim();
    return `<?php

/**
 * Registers an ACF block for this component.
 *
 * @link https://www.advancedcustomfields.com/resources/acf_register_block_type/
 */
acf_register_block_type([
    'name' => '${name}',
    'title' => '${title}',
    'description' => '',
    'category' => 'theme-blocks',
    'icon' => 'admin-generic',
    'mode' => 'auto',
    'supports' => [
        'anchor' => true,
        'align' => false,
    ],
    'render_callback' => function ($block, $content, $is_preview, $post_id) {
        $args = \\Geum\\Component::generateArgsFromBlock($block, get_fields());

        echo \\Geum\\Components\\${className}::make(...$args);
    },
]);
`;
}

/**
 * Regenerate component imports
 */
function regenerateImports() {
    try {
        execSync('npm run build -- --mode development 2>/dev/null || true', {
            cwd: path.join(__dirname, '..'),
            stdio: 'ignore'
        });
    } catch {
        // Silently fail - imports will regenerate on next dev/build
    }
}

/**
 * Main
 */
function main() {
    const args = process.argv.slice(2);

    if (args.length === 0 || args[0] === '--help' || args[0] === '-h') {
        console.log(`
Usage: node dev-scripts/scaffold-component.js <name> [options]

Options:
  --styles    Create styles.pcss
  --scripts   Create scripts.js
  --block     Create acf.php for ACF block
  --all       Create all optional files

Examples:
  node dev-scripts/scaffold-component.js hero-banner
  node dev-scripts/scaffold-component.js hero-banner --styles --block
  node dev-scripts/scaffold-component.js hero-banner --all
`);
        process.exit(0);
    }

    const name = args[0];
    const flags = args.slice(1);

    // Validate name
    if (!/^[a-z][a-z0-9-]*$/.test(name)) {
        console.error('Error: Component name must be lowercase with hyphens (e.g., my-component)');
        process.exit(1);
    }

    const className = toPascalCase(name);
    const componentDir = path.join(COMPONENTS_DIR, name);

    // Check if exists
    if (fs.existsSync(componentDir)) {
        console.error(`Error: Component '${name}' already exists`);
        process.exit(1);
    }

    const withStyles = flags.includes('--styles') || flags.includes('--all');
    const withScripts = flags.includes('--scripts') || flags.includes('--all');
    const withBlock = flags.includes('--block') || flags.includes('--all');

    // Create directory
    fs.mkdirSync(componentDir, { recursive: true });
    console.log(`Created: components/${name}/`);

    // Create class
    fs.writeFileSync(
        path.join(componentDir, `${className}.php`),
        generateClass(name, className)
    );
    console.log(`Created: ${className}.php`);

    // Create template
    fs.writeFileSync(
        path.join(componentDir, 'template.php'),
        generateTemplate(name, className)
    );
    console.log(`Created: template.php`);

    // Optional: styles
    if (withStyles) {
        fs.writeFileSync(
            path.join(componentDir, 'styles.pcss'),
            generateStyles(name)
        );
        console.log(`Created: styles.pcss`);
    }

    // Optional: scripts
    if (withScripts) {
        fs.writeFileSync(
            path.join(componentDir, 'scripts.js'),
            generateScripts(name)
        );
        console.log(`Created: scripts.js`);
    }

    // Optional: ACF block
    if (withBlock) {
        fs.writeFileSync(
            path.join(componentDir, 'acf.php'),
            generateAcfBlock(name, className)
        );
        console.log(`Created: acf.php`);
    }

    console.log(`\nComponent '${name}' scaffolded successfully.`);

    // Usage hint
    console.log(`\nUsage:`);
    console.log(`  use Geum\\Components\\${className};`);
    console.log(`  echo ${className}::make();`);

    if (withStyles) {
        console.log(`\nStyles will be auto-imported on next dev/build.`);
    }
}

main();
