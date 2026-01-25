/**
 * Register block styles for core/heading block.
 */
wp.domReady(() => {
    wp.blocks.registerBlockStyle('core/heading', {
        name: 'type-h2',
        label: 'H2 Appearance',
    });

    wp.blocks.registerBlockStyle('core/heading', {
        name: 'type-h3',
        label: 'H3 Appearance',
    });

    wp.blocks.registerBlockStyle('core/heading', {
        name: 'type-h4',
        label: 'H4 Appearance',
    });

    wp.blocks.registerBlockStyle('core/heading', {
        name: 'type-h5',
        label: 'H5 Appearance',
    });
});
