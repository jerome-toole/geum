/**
 * Unregister unwanted rich text formats.
 */
wp.domReady(() => {
    wp.richText.unregisterFormatType('core/code');
    wp.richText.unregisterFormatType('core/image');
    wp.richText.unregisterFormatType('core/keyboard');
    // wp.richText.unregisterFormatType("core/strikethrough");
    // wp.richText.unregisterFormatType("core/subscript");
    // wp.richText.unregisterFormatType("core/superscript");
    wp.richText.unregisterFormatType('core/text-color');
});
