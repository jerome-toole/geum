/**
 * Unregister default styles for core/quote block.
 */
wp.domReady(() => {
    wp.blocks.unregisterBlockStyle('core/quote', 'default');
    wp.blocks.unregisterBlockStyle('core/quote', 'plain');
});
