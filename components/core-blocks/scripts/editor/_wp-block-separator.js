/**
 * Unregister default styles for core/separator block.
 */
wp.domReady(() => {
    wp.blocks.unregisterBlockStyle('core/separator', 'default');
    wp.blocks.unregisterBlockStyle('core/separator', 'dots');
    wp.blocks.unregisterBlockStyle('core/separator', 'wide');
});
