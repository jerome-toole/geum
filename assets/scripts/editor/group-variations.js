/**
 * Unregister any group variants that aren't in the allow list.
 * Note: The default group cannot be unregistered independently of the other variants.
 */
wp.domReady(() => {
    const allowedGroupVariants = [
        'group-stack',
        // 'group-row',
    ];

    wp.blocks.getBlockVariations('core/group').forEach((variant) => {
        if (!allowedGroupVariants.includes(variant.name)) {
            wp.blocks.unregisterBlockVariation('core/group', variant.name);
        }
    });
});
