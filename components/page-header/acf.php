<?php

/**
 * Registers an ACF block for this component. Autoloaded via the `acf/init` hook.
 *
 * It is important that this file remain free from HTML and is merely a mechanism for:
 * - Registering a block.
 * - Retrieving and processing block fields in the required format.
 * - Rendering the component
 *
 * @see /geum/Theme/Plugins/ACF.php
 * @link https://www.advancedcustomfields.com/resources/acf_register_block_type/
 */
acf_register_block_type([
    'name' => 'page-header', // Name/key, alphanumeric characters and dashes only.
    'title' => 'Page Header', // Label in block editor.
    // 'description' => '', // A short description.
    'category' => 'theme-blocks', // Core: common | formatting | layout | widgets | embed.
    'icon' => 'cover-image', // https://developer.wordpress.org/resource/dashicons/

    // Optional keywords to help users search for the block
    'keywords' => [
        'hero',
    ],

    // The post types that can use this block.
    'post_types' => [
        'page',
        'geum-template',
    ],

    'mode' => 'auto', // edit | preview | auto.
    'align' => 'full', // Default block alignment: wide | full | center | left | right.

    // An array of block features to support.
    'supports' => [
        'anchor' => true,
        'align' => false, // Block alignment choices. 'false' to hide all.
        // 'align_text' => true,
        // 'align_content' => 'matrix',
        'color' => [
            'background' => false,
            'text' => false,
            'gradients' => false,
            // 'link' => false,
        ],
        'multiple' => false, // Allows multiple instances of this block in one post. Default: true.
        // 'lock' => false, // Allows users to lock or unlock this block. Default: true.
    ],

    // Handle rendering the block
    'render_callback' => function ($block, $content, $is_preview, $post_id) {
        echo \Geum\Components\PageHeader::fromBlock($block, get_fields(), $content, $is_preview, $post_id);
    },
]);
