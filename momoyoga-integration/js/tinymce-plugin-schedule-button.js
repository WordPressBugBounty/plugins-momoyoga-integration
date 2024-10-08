/* global tinymce */
(function() {
	tinymce.create( 'tinymce.plugins.momoyoga_schedule', {

		init : function( editor ) {
			editor.addButton( 'momoyoga_schedule', {
				title : 'Add Momoyoga schedule',
				cmd   : 'momoyoga_add_schedule',
				icon  : 'momoyoga'
			});
			editor.addCommand('momoyoga_add_schedule', function() {
                editor.execCommand( 'mceInsertContent', 0, '[momoyoga-schedule /]' );
			});
		},

		createControl : function() {
			return null;
		},

		getInfo : function() {
			return {
				longname : 'Momoyoga Schedule',
				author   : 'Automattic',
				version  : '1'
			};
		}
	});

	tinymce.PluginManager.add( 'momoyoga_schedule', tinymce.plugins.momoyoga_schedule );
})();