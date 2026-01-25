/**
 * Register block styles for core/heading block.
 */
wp.domReady(() => {
    wp.blocks.registerBlockStyle('acf/logo-grid', {
        name: 'thin',
        label: 'Thin',
    });
});
