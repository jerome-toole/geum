const { createHigherOrderComponent } = wp.compose;

// Remove the row, stack, and grid variations from core/group. Use the columns block, which provides the right amount of layout control for most use cases.
wp.hooks.addFilter('blocks.registerBlockType', 'geum/remove-group-variations', (settings, name) => {
    if (name === 'core/group') {
        console.log(settings.variations);

        settings.variations = settings.variations?.filter(
            (v) => !['group-row', 'group-stack', 'group-grid'].includes(v.name)
        );
    }
    return settings;
});

/**
 * Remove layout editing from core/group by default.
 * The grid variation re-enables it via the filter below.
 */
wp.hooks.addFilter(
    'blocks.registerBlockType',
    'geum/disable-group-content-width',
    (settings, name) => {
        if (name !== 'core/group') return settings;

        return {
            ...settings,
            supports: {
                ...settings.supports,
                layout: {
                    ...settings.supports?.layout,
                    allowEditing: false,
                },
            },
        };
    }
);

/**
 * Re-enable layout editing for the grid variation of core/group.
 * Mutates the block type registry only when the block is selected,
 * ensuring the inspector reads the correct value for the focused block.
 */
wp.hooks.addFilter(
    'editor.BlockEdit',
    'geum/grid-group-layout-editing',
    createHigherOrderComponent(
        (BlockEdit) => (props) => {
            if (props.name === 'core/group' && props.isSelected) {
                const blockType = wp.blocks.getBlockType('core/group');
                if (blockType?.supports?.layout) {
                    blockType.supports.layout.allowEditing =
                        props.attributes?.layout?.type === 'grid';
                }
            }

            return wp.element.createElement(BlockEdit, props);
        },
        'withGridGroupLayoutEditing'
    )
);
