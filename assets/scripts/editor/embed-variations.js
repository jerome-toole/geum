/**
 * Unregister any embed variants that aren't in the allow list.
 * Note: The default embed (which allows any provider) cannot be unregistered.
 */
wp.domReady(() => {
    const allowedEmbedVariants = ['youtube', 'vimeo', 'gravity-forms'];

    wp.blocks.getBlockVariations('core/embed').forEach((variant) => {
        if (!allowedEmbedVariants.includes(variant.name)) {
            wp.blocks.unregisterBlockVariation('core/embed', variant.name);
        }
    });
});
