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
    'name' => 'group', // Name/key, alphanumeric characters and dashes only.
    'title' => 'Group', // Label in block editor.
    // 'description' => '', // A short description.
    'category' => 'theme-blocks', // Core: common | formatting | layout | widgets | embed.
    'icon' => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="24" height="24" aria-hidden="true" focusable="false"><path d="M18 4h-7c-1.1 0-2 .9-2 2v3H6c-1.1 0-2 .9-2 2v7c0 1.1.9 2 2 2h7c1.1 0 2-.9 2-2v-3h3c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-4.5 14c0 .3-.2.5-.5.5H6c-.3 0-.5-.2-.5-.5v-7c0-.3.2-.5.5-.5h3V13c0 1.1.9 2 2 2h2.5v3zm0-4.5H11c-.3 0-.5-.2-.5-.5v-2.5H13c.3 0 .5.2.5.5v2.5zm5-.5c0 .3-.2.5-.5.5h-3V11c0-1.1-.9-2-2-2h-2.5V6c0-.3.2-.5.5-.5h7c.3 0 .5.2.5.5v7z"></path></svg>',

    // Optional keywords to help users search for the block
    'keywords' => [
        'cover',
        'background',
    ],

    // The post types that can use this block.
    // 'post_types' => [
    //     'post',
    //     'page',
    //     'geum-template',
    // ],

    'mode' => 'preview', // edit | preview | auto.
    'align' => 'wide', // Default block alignment: wide | full | center | left | right.

    // An array of block features to support.
    'supports' => [
        'anchor' => true,
        'align' => ['wide', 'full'], // Block alignment choices. 'false' to hide all.
        // 'align_text' => true,
        // 'align_content' => 'matrix',
        'color' => [
            'background' => true,
            'text' => false,
            'gradients' => false,
            // 'link' => false,
        ],
        'jsx' => true, // InnerBlocks
    ],

    // Handle rendering the block
    'render_callback' => function ($block, $content, $is_preview, $post_id) {
        $args = \Geum\Component::generateArgsFromBlock($block, get_fields());

        echo \Geum\Components\Group::make(...$args);
    },
]);
