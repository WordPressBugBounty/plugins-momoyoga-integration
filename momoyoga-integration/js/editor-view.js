/* global tinyMCE, QTags, wp */
( function( $, wp, momoyogaScheduleEditorView ) {
    wp.mce = wp.mce || {};
	if ( 'undefined' === typeof wp.mce.views ) {
		return;
	}

	wp.mce.momoyoga_wp_view_renderer = {
		shortcode_string : 'momoyoga-schedule',
        template       : wp.template( 'momoyoga-schedule' ),
		editor_inline  : wp.template( 'momoyoga-schedule-editor-inline' ),

		getContent     : function() {
			var schedule_url = this.shortcode.attrs.named.schedule_url,
				body = momoyogaScheduleEditorView.labels['submit_button_text'];

			if (schedule_url)
				body += ' for <a href="' + schedule_url + '" target="_blank">' + schedule_url + '</a>';

			options = {
				body : body,
				submit_button_text : "Submit"
			};				
                
			return this.template (options);
		},

		edit: function( data, update_callback ) {
			var shortcode_data = wp.shortcode.next( this.shortcode_string, data ),
				shortcode = shortcode_data.shortcode,
				$tinyMCE_document = $( tinyMCE.activeEditor.getDoc() ),
				$view = $tinyMCE_document.find('.wpview.wpview-wrap').filter(function(){
					return $(this).attr('data-mce-selected');
				}),
				$template = this.editor_inline,
				$editframe = $('<iframe scrolling="no" class="inline-edit-momoyoga-schedule" />'),
				index = 0,
				$stylesheet = $( '<link rel="stylesheet" href="' + momoyogaScheduleEditorView.inline_editing_style + '" />' );

			while ( field = wp.shortcode.next( 'momoyoga-schedule', shortcode.content, index ) ) {
				index = field.index + field.content.length;
			}

			$editframe.on('load', function(){
				var $this = $(this);

				$this.contents().find('head').append( $stylesheet )
				$stylesheet.on( 'load', function(){
					$this.trigger('checkheight');
				});
	
				$this.contents().find('body').html( $template( {
					schedule_url : shortcode.attrs.named.schedule_url,
				}) );

				$this.on('checkheight', function(){
					this.style.height = '10px';
					this.style.height = ( 18 + this.contentWindow.document.body.scrollHeight ) + 'px';
					tinyMCE.activeEditor.execCommand('wpAutoResize');
				}).trigger('checkheight').hide().fadeIn(250, function(){
					$this.contents().find('input:first').focus();
				});
	
				var $buttons = $this.contents().find('.buttons');
	
				// The 'save' listener.
				$buttons.find('input[name=submit]').on( 'click', function(){
					var new_data = shortcode;
					new_data.type = 'closed';
					new_data.attrs = {};
					new_data.content = null;
	
					if ( $this.contents().find('input[name=schedule_url]').val() ) {
						new_data.attrs.schedule_url = $this.contents().find('input[name=schedule_url]').val();
					}
	
					update_callback( wp.shortcode.string( new_data ) );
				});
	
				$buttons.find('input[name=cancel]').on( 'click', function(){
					update_callback( wp.shortcode.string( shortcode ) );
				});
			});

			$view.html( $editframe );
		}
	};
	wp.mce.views.register( 'momoyoga-schedule', wp.mce.momoyoga_wp_view_renderer );

	// Add the 'text' editor button.
	QTags.addButton(
		'schedule_shortcode',
		'momoyoga schedule',
		function() {
			QTags.insertContent( '[momoyoga-schedule schedule_url="https://www.momoyoga.com/mystudioname" /]' );
		}
	);

	var $wp_content_wrap = $( '#wp-content-wrap' );
	$( '#insert-momoyoga-schedule' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( $wp_content_wrap.hasClass( 'tmce-active' ) ) {
			tinyMCE.execCommand( 'momoyoga_add_schedule' );
		} else if ( $wp_content_wrap.hasClass( 'html-active' ) ) {
			QTags.insertContent( '[momoyoga-schedule schedule_url="https://www.momoyoga.com/mystudioname" /]' );
		} else {
			window.console.error( 'Neither TinyMCE nor QuickTags is active. Unable to insert schedule.' );
		}
	} );
}( jQuery, wp, momoyogaScheduleEditorView ) );
