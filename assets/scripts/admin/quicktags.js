/* global acf */

if ('acf' in window && 'add_filter' in acf) {
    acf.add_filter('wysiwyg_quicktags_settings', function addQuicktagsSettings(qtInit, id, field) {
        if (field[0]) {
            const editorWrap = field[0].querySelector('[data-toolbar]');

            if (editorWrap.getAttribute('data-toolbar') === 'basic_formatting') {
                qtInit.buttons = 'strong,em,link,close';
            } else if (editorWrap.getAttribute('data-toolbar') === 'extended_formatting') {
                qtInit.buttons = 'strong,em,link,ul,ol,li,close';
            }
        }

        return qtInit;
    });
}
