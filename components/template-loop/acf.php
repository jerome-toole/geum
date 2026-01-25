<?php

/**
 * Registers an ACF block for this component. Autoloaded via the `acf/init` hook.
 *
 * It is important that this file remain free from HTML and is merely a mechanism for:
 * - Registering a block.
 * - Retrieving and processing block fields in the required format.
 * - Rendering the component
 *
 * @see: /geum/Theme/Plugins/ACF.php
 *
 * @link: https://www.advancedcustomfields.com/resources/acf_register_block_type/
 */
acf_register_block_type([
    'name' => 'template-loop', // Name/key, alphanumeric characters and dashes only.
    'title' => 'Template Loop', // Label in block editor.
    // 'description' => '', // A short description.
    'category' => 'theme-blocks', // Core: common | formatting | layout | widgets | embed.
    'icon' => 'controls-repeat', // https://developer.wordpress.org/resource/dashicons/

    // Optional keywords to help users search for the block
    // 'keywords' => [
    //     'quote',
    //     'mention',
    //     'cite',
    // ],

    // The post types that can use this block.
    'post_types' => [
        'geum-template',
    ],

    'mode' => 'edit', // edit | preview | auto.
    'align' => 'full', // Default block alignment: wide | full | center | left | right.

    // An array of block features to support.
    'supports' => [
        'align' => false, // Block alignment choices. 'false' to hide all.
        // 'align_text' => true,
        // 'align_content' => 'matrix',
        'color' => [
            'background' => true,
            'text' => false,
            'gradients' => false,
            // 'link' => false,
        ],
        'multiple' => false, // Allows multiple instances of this block in one post. Default: true.
    ],

    // Handle rendering the block
    'render_callback' => function ($block, $content, $is_preview, $post_id) {
        $args = \Geum\Component::generateArgsFromBlock($block, get_fields());

        echo \Geum\Components\TemplateLoop::make(...$args);
    },
]);
