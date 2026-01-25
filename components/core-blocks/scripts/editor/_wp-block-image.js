/**
 * Unregister default styles for core/image block.
 */
wp.domReady(() => {
    wp.blocks.unregisterBlockStyle('core/image', 'default');
    wp.blocks.unregisterBlockStyle('core/image', 'rounded');
});
