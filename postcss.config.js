import tailwindcss from '@tailwindcss/postcss';
import autoprefixer from 'autoprefixer';
import postcssComment from 'postcss-comment';
import postcssFunctions from 'postcss-functions';
import postcssImport from 'postcss-import';
import postcssSimpleVars from 'postcss-simple-vars';
import postcssColorSystem from './dev-scripts/postcss-color-system.js';
import functionsConfig from './dev-scripts/postcss-functions-config.js';
import postcssGlobImport from './dev-scripts/postcss-glob-import.js';

const config = {
    parser: postcssComment,
    plugins: [
        postcssGlobImport(),
        postcssImport(),
        postcssColorSystem(),
        postcssFunctions(functionsConfig),
        postcssSimpleVars(),
        tailwindcss(),
        autoprefixer(),
    ],
};

export default config;
