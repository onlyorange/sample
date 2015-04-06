(function() {
    tinymce.PluginManager.add('sw_lineBreak', function( editor, url ) {
        editor.addButton( 'sw_lineBreak', {
            text: 'Add Line Break',
            icon: false,
            wrapper: true,
            onclick: function() {
                editor.insertContent('<dfn class="mce-lb"></dfn> ', 'raw');
            }
        });
    });
})();
