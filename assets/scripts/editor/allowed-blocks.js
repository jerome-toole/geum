/**
 * A list of core blocks that should NOT be unregistered.
 *
 * Core blocks are unregistered by default to ensure that all blocks that are
 * allowed are fully supported and required in the project.
 */
const allowedCoreBlocks = [
    'core/paragraph',
    'core/image',
    'core/heading',
    'core/gallery',
    'core/list',
    'core/list-item',
    'core/quote',
    'core/shortcode',
    // 'core/archives',
    // 'core/audio',
    'core/button',
    'core/buttons',
    // 'core/calendar',
    // 'core/categories',
    // 'core/code',
    'core/columns',
    'core/column',
    'core/cover',
    'core/embed',
    // 'core/file',
    // 'core/group',
    'core/freeform',
    'core/html',
    // 'core/media-text',
    // 'core/latest-comments',
    // 'core/latest-posts',
    'core/missing',
    // 'core/more',
    // 'core/nextpage',
    // 'core/page-list',
    // 'core/preformatted',
    // 'core/pullquote',
    // 'core/rss',
    // 'core/search',
    'core/separator',
    'core/block',
    // 'core/social-links',
    // 'core/social-link',
    // 'core/spacer',
    'core/table',
    // 'core/tag-cloud',
    // 'core/text-columns',
    // 'core/verse',
    // 'core/video',
    // 'core/site-logo',
    // 'core/site-tagline',
    // 'core/site-title',
    // 'core/query',
    // 'core/post-template',
    // 'core/query-title',
    // 'core/query-pagination',
    // 'core/query-pagination-next',
    // 'core/query-pagination-numbers',
    // 'core/query-pagination-previous',
    // 'core/post-title',
    // 'core/post-content',
    // 'core/post-date',
    // 'core/post-excerpt',
    // 'core/post-featured-image',
    // 'core/post-terms',
    // 'core/loginout',
];

/**
 * A list of non-core blocks that SHOULD be unregistered,
 * e.g. unwanted blocks added by plugins.
 */
const disallowedBlocks = [
    // 'example/block-name',
];

wp.domReady(() => {
    wp.blocks.getBlockTypes().forEach((blockType) => {
        if (blockType.name.indexOf('core/') === 0) {
            if (allowedCoreBlocks.indexOf(blockType.name) === -1) {
                wp.blocks.unregisterBlockType(blockType.name);
            }
        } else if (disallowedBlocks.indexOf(blockType.name) >= 0) {
            wp.blocks.unregisterBlockType(blockType.name);
        }
    });
});
