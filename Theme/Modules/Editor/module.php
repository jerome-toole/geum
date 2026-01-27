<?php

namespace Theme\Modules\Editor;

class Module
{
    public static function init(): void
    {
        \add_filter('geum/editor/allowed_blocks', [__CLASS__, 'allowedBlocks']);
    }

    public static function allowedBlocks(array $blocks): array
    {
        return array_merge($blocks, [
            // Text
            'core/paragraph',
            'core/heading',
            'core/list',
            'core/list-item',
            'core/quote',
            // 'core/pullquote',
            // 'core/verse',
            // 'core/preformatted',
            // 'core/code',
            'core/table',

            // Media
            'core/image',
            // 'core/gallery',
            // 'core/audio',
            // 'core/video',
            // 'core/file',
            // 'core/cover',
            // 'core/media-text',

            // Layout
            'core/buttons',
            'core/button',
            'core/columns',
            'core/column',
            'core/group',
            'core/row',
            'core/stack',
            'core/separator',
            // 'core/spacer',

            // Embeds
            'core/embed',
            'core/shortcode',
            'core/html',
            'core/freeform',

            // Reusable
            'core/block',
            'core/missing',
        ]);
    }
}
