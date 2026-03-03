/**
 * Remove the 'Inner blocks use content width' option from core/group.
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
