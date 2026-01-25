#!/usr/bin/env node

/**
 * Component Class Generator
 *
 * Generates typed PHP component classes from existing functions.php files,
 * automatically migrating filterArgs logic.
 *
 * Usage:
 *   node dev-scripts/generate-component-class.js accordion
 *   node dev-scripts/generate-component-class.js card --delete-old
 *   node dev-scripts/generate-component-class.js --all
 */

import fs from 'fs';
import path from 'path';
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
 * Parse functions.php to extract default args from array_merge
 */
function parseDefaultArgs(content) {
    // Match array_merge([ ... ], $args)
    const mergeMatch = content.match(/\$args\s*=\s*array_merge\s*\(\s*\[([\s\S]*?)\]\s*,\s*\$args\s*\)/);

    if (!mergeMatch) {
        return [];
    }

    const argsBlock = mergeMatch[1];
    const args = [];

    // Parse each 'key' => value line - handle multi-line values
    let currentKey = null;
    let currentValue = '';
    let bracketDepth = 0;

    const lines = argsBlock.split('\n');
    for (const line of lines) {
        const trimmed = line.trim();

        // Skip empty lines and comments
        if (!trimmed || trimmed.startsWith('//')) continue;

        // Count brackets to handle nested arrays
        const openBrackets = (trimmed.match(/\[/g) || []).length;
        const closeBrackets = (trimmed.match(/\]/g) || []).length;

        if (currentKey === null) {
            // Look for new key => value
            const match = trimmed.match(/^'([^']+)'\s*=>\s*(.*)$/);
            if (match) {
                currentKey = match[1];
                currentValue = match[2];

                // Check if value is complete (ends with comma, no unclosed brackets)
                bracketDepth = openBrackets - closeBrackets;

                if (bracketDepth === 0 && (currentValue.endsWith(',') || currentValue.endsWith('],'))) {
                    // Complete single-line value
                    args.push({
                        name: currentKey.replace(/-/g, '_'),
                        originalName: currentKey,
                        defaultValue: currentValue.replace(/,$/, '').trim(),
                        type: inferType(currentValue.trim()),
                    });
                    currentKey = null;
                    currentValue = '';
                }
            }
        } else {
            // Continue multi-line value
            currentValue += '\n' + line;
            bracketDepth += openBrackets - closeBrackets;

            if (bracketDepth <= 0) {
                // Value complete
                args.push({
                    name: currentKey.replace(/-/g, '_'),
                    originalName: currentKey,
                    defaultValue: currentValue.replace(/,\s*$/, '').trim(),
                    type: inferType(currentValue.trim()),
                });
                currentKey = null;
                currentValue = '';
                bracketDepth = 0;
            }
        }
    }

    return args;
}

/**
 * Infer PHP type from default value
 */
function inferType(value) {
    const v = value.trim().replace(/,$/, '');
    if (v === '[]' || v.startsWith('[')) return 'array';
    if (v === "''" || v.startsWith("'")) return 'string';
    if (v === 'true' || v === 'false') return 'bool';
    if (v === 'null') return 'mixed';
    if (/^\d+$/.test(v)) return 'int';
    if (/^\d+\.\d+$/.test(v)) return 'float';
    return 'mixed';
}

/**
 * Extract the full filterArgs function body
 */
function extractFilterArgsBody(content) {
    // Find the function start
    const funcStart = content.indexOf('function filterArgs(');
    if (funcStart === -1) return null;

    // Find the opening brace
    const braceStart = content.indexOf('{', funcStart);
    if (braceStart === -1) return null;

    // Find matching closing brace
    let depth = 1;
    let pos = braceStart + 1;
    while (pos < content.length && depth > 0) {
        if (content[pos] === '{') depth++;
        if (content[pos] === '}') depth--;
        pos++;
    }

    return content.substring(braceStart + 1, pos - 1);
}

/**
 * Extract required classes from filterArgs body
 */
function extractRequiredClasses(body) {
    // Match: $args['classes'] = array_merge([ 'class1', 'class2' ], $args['classes']);
    const match = body.match(/\$args\['classes'\]\s*=\s*array_merge\s*\(\s*\[([\s\S]*?)\]\s*,\s*\$args\['classes'\]\s*\)/);

    if (!match) return [];

    const classesStr = match[1];
    const classes = [];

    // Extract string classes
    const classMatches = classesStr.matchAll(/'([^']+)'/g);
    for (const m of classMatches) {
        classes.push(m[1]);
    }

    return classes;
}

/**
 * Clean and transform filterArgs body for the new class
 */
function transformFilterArgsBody(body, componentName) {
    if (!body) return '';

    let transformed = body;

    // Remove default arguments section
    transformed = transformed.replace(
        /\s*\/\/\s*-+\s*\n\s*\/\/\s*Default arguments\.?\s*\n\s*\/\/\s*-+\s*\n\s*\$args\s*=\s*array_merge\s*\(\s*\[[\s\S]*?\]\s*,\s*\$args\s*\)\s*;/g,
        ''
    );

    // Remove required classes section (we'll add it cleanly)
    transformed = transformed.replace(
        /\s*\/\/\s*-+\s*\n\s*\/\/\s*Required classes\.?\s*\n\s*\/\/\s*-+\s*\n\s*\$args\['classes'\]\s*=\s*array_merge\s*\(\s*\[[\s\S]*?\]\s*,\s*\$args\['classes'\]\s*\)\s*;/g,
        ''
    );

    // Remove the final return section comment and return statement
    transformed = transformed.replace(
        /\s*\/\/\s*-+\s*\n\s*\/\/\s*Return the filtered args\.?\s*\n\s*\/\/\s*-+\s*\n\s*return \$args\s*;/g,
        ''
    );

    // Also remove standalone return $args;
    transformed = transformed.replace(/\s*return \$args\s*;\s*$/g, '');

    // Clean up excessive blank lines
    transformed = transformed.replace(/\n{3,}/g, '\n\n');

    // Trim
    transformed = transformed.trim();

    return transformed;
}

/**
 * Format the filterArgs method body with proper indentation
 */
function formatFilterArgsMethod(requiredClasses, transformedBody) {
    let lines = [];

    // Required classes
    if (requiredClasses.length > 0) {
        lines.push('        // Required classes');
        lines.push("        $args['classes'] = array_merge([");
        for (const cls of requiredClasses) {
            lines.push(`            '${cls}',`);
        }
        lines.push("        ], $args['classes'] ?? []);");
    } else {
        lines.push('        // Required classes');
        lines.push("        $args['classes'] = $args['classes'] ?? [];");
    }

    // Add transformed body if exists
    if (transformedBody) {
        lines.push('');

        // Indent the transformed body properly
        const bodyLines = transformedBody.split('\n');
        for (const line of bodyLines) {
            // Preserve relative indentation but ensure base indent of 8 spaces
            const trimmed = line.trimStart();
            const originalIndent = line.length - line.trimStart().length;

            if (trimmed === '') {
                lines.push('');
            } else {
                // Convert original 4-space indentation to 8-space base
                const extraIndent = Math.max(0, originalIndent - 4);
                lines.push('        ' + ' '.repeat(extraIndent) + trimmed);
            }
        }
    }

    lines.push('');
    lines.push('        return $args;');

    return lines.join('\n');
}

/**
 * Generate the component class file content
 */
function generateComponentClass(componentName, args, requiredClasses, filterArgsBody) {
    // For nested components like menu/menu-item:
    // - componentName = "menu/menu-item"
    // - className = "MenuItem"
    // - namespace = "Geum\Components\Menu\MenuItem"
    const parts = componentName.split('/');
    const pascalParts = parts.map(toPascalCase);
    const className = pascalParts[pascalParts.length - 1];
    const namespace = 'Geum\\\\Components\\\\' + pascalParts.join('\\\\');

    // Build typed parameters for make() method
    const makeParams = args.map(arg => {
        const phpName = arg.name.replace(/-/g, '_');
        return `        ${arg.type} $${phpName} = ${arg.defaultValue},`;
    }).join('\n');

    // Format the filterArgs method
    const filterArgsMethodBody = formatFilterArgsMethod(requiredClasses, filterArgsBody);

    // Build the class
    return `<?php

namespace ${namespace.replace(/\\\\/g, '\\')};

use Geum\\Component;
use Geum\\ComponentBase;

/**
 * ${className} Component
 *
 * Usage:
 *   use ${namespace.replace(/\\\\/g, '\\')}\\${className};
 *
 *   echo ${className}::make();
 */
class ${className} extends ComponentBase
{
    protected static string $name = '${componentName}';

    /**
     * Create a new ${className} component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
${makeParams}
    ): ?static {
        $args = get_defined_vars();

        $args = static::processArgs($args);

        if ($args === null) {
            return null;
        }

        return new static($args);
    }

    /**
     * Filter and transform args before rendering.
     *
     * Return null to prevent the component from rendering.
     */
    protected static function filterArgs(array $args): ?array
    {
${filterArgsMethodBody}
    }
}
`;
}

/**
 * Process a single component
 */
function processComponent(componentName, options = {}) {
    const componentDir = path.join(COMPONENTS_DIR, ...componentName.split('/'));

    if (!fs.existsSync(componentDir)) {
        console.error(`❌ Component directory not found: ${componentName}`);
        return false;
    }

    const functionsPath = path.join(componentDir, 'functions.php');

    // For nested components like menu/menu-item, class name is MenuItem
    const parts = componentName.split('/');
    const className = toPascalCase(parts[parts.length - 1]);
    const classPath = path.join(componentDir, `${className}.php`);

    // Check if class already exists
    if (fs.existsSync(classPath) && !options.force) {
        console.log(`⏭️  ${componentName}: Class already exists (use --force to overwrite)`);
        return false;
    }

    let args = [];
    let requiredClasses = [parts[parts.length - 1]]; // Default to last part of name
    let filterArgsBody = '';

    // Parse existing functions.php if it exists
    if (fs.existsSync(functionsPath)) {
        const content = fs.readFileSync(functionsPath, 'utf8');
        args = parseDefaultArgs(content);

        const fullBody = extractFilterArgsBody(content);
        if (fullBody) {
            const extractedClasses = extractRequiredClasses(fullBody);
            if (extractedClasses.length > 0) {
                requiredClasses = extractedClasses;
            }
            filterArgsBody = transformFilterArgsBody(fullBody, componentName);
        }

        console.log(`📖 Parsed ${args.length} args, ${requiredClasses.length} classes from functions.php`);
    } else {
        console.log(`📝 No functions.php found, creating minimal class`);
    }

    // Ensure 'classes' is always present in args
    if (!args.find(a => a.name === 'classes')) {
        args.unshift({
            name: 'classes',
            originalName: 'classes',
            defaultValue: '[]',
            type: 'array',
        });
    }

    // Generate and write the class
    const classContent = generateComponentClass(componentName, args, requiredClasses, filterArgsBody);
    fs.writeFileSync(classPath, classContent);
    console.log(`✅ Created: ${className}.php`);

    // Optionally delete old functions.php
    if (options.deleteOld && fs.existsSync(functionsPath)) {
        fs.unlinkSync(functionsPath);
        console.log(`🗑️  Deleted: functions.php`);
    }

    return true;
}

/**
 * List all components (including nested ones like menu/menu-item)
 */
function listComponents() {
    const components = [];

    function scanDir(dir, prefix = '') {
        const entries = fs.readdirSync(dir, { withFileTypes: true });

        for (const entry of entries) {
            if (!entry.isDirectory() || entry.name.startsWith('_')) continue;

            const fullPath = path.join(dir, entry.name);
            const componentPath = prefix ? `${prefix}/${entry.name}` : entry.name;

            // Check if this is a component (has template.php)
            const hasTemplate = fs.existsSync(path.join(fullPath, 'template.php'));

            if (hasTemplate) {
                components.push(componentPath);
            }

            // Check for nested components (but skip known non-component dirs)
            const skipDirs = ['scripts', 'styles', 'functions'];
            if (!skipDirs.includes(entry.name)) {
                scanDir(fullPath, componentPath);
            }
        }
    }

    scanDir(COMPONENTS_DIR);
    return components;
}

// CLI handling
const args = process.argv.slice(2);

if (args.length === 0) {
    console.log(`
Component Class Generator
─────────────────────────

Automatically generates typed PHP component classes from existing functions.php files,
migrating all filterArgs logic.

Usage:
  node generate-component-class.js <component-name>     Generate single component
  node generate-component-class.js --all               Generate all components
  node generate-component-class.js --list              List all components

Options:
  --force        Overwrite existing class files
  --delete-old   Delete functions.php after generating class

Examples:
  node generate-component-class.js accordion
  node generate-component-class.js card --force
  node generate-component-class.js --all --delete-old
`);
    process.exit(0);
}

const options = {
    force: args.includes('--force'),
    deleteOld: args.includes('--delete-old'),
};

if (args.includes('--list')) {
    console.log('\nComponents:\n');
    listComponents().forEach(c => console.log(`  ${c}`));
    console.log('');
    process.exit(0);
}

if (args.includes('--all')) {
    console.log('\n🚀 Generating all component classes...\n');
    const components = listComponents();
    let success = 0;
    let skipped = 0;

    for (const component of components) {
        console.log(`\n📦 ${component}`);
        if (processComponent(component, options)) {
            success++;
        } else {
            skipped++;
        }
    }

    console.log(`\n${'─'.repeat(40)}`);
    console.log(`✨ Done! Generated: ${success}, Skipped: ${skipped}\n`);
} else {
    const componentName = args.find(a => !a.startsWith('--'));
    if (componentName) {
        console.log(`\n📦 ${componentName}`);
        processComponent(componentName, options);
        console.log('');
    }
}
