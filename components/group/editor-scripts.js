/**
 * Register block styles for core/heading block.
 */
wp.domReady(() => {
    wp.blocks.registerBlockStyle('acf/group', {
        name: 'thin',
        label: 'Thin',
    });
});
