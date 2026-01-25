/**
 * Unregister default styles for core/table block.
 */
wp.domReady(() => {
    wp.blocks.unregisterBlockStyle('core/table', 'regular'); // Non-standard 'default' style name.
    wp.blocks.unregisterBlockStyle('core/table', 'stripes');
});
