<?php
load_plugin_textdomain( 'momoyoga-integration', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );

class MomoyogaEditorButton
{
    /**
     * Add hooks according to screen.
     *
     * @param WP_Screen $screen Data about current screen.
     */
    public function add_hooks() {
        wp_enqueue_style( 'momoyoga-admin', plugins_url( 'css/admin.css', __FILE__ ) );
        
        add_filter( 'mce_external_plugins', array( $this, 'mce_external_plugins' ) );
		add_filter( 'mce_buttons', array( $this, 'mce_buttons' ) );
        
        add_action( 'admin_notices', array( $this, 'handle_editor_view_js' ) );
        add_action( 'media_buttons', array( $this, 'momoyoga_schedule_button' ), 9999 );
    }

	function mce_external_plugins( $plugin_array ) {
		$plugin_array['momoyoga_schedule'] =  plugins_url( 'js/tinymce-plugin-schedule-button.js', __FILE__ );
		return $plugin_array;
	}

	function mce_buttons( $buttons ) {
		return array_merge(
			$buttons,
			array( 'momoyoga_schedule' )
		);
	}

    function momoyoga_schedule_button() {
        $title = __( 'Add Momoyoga schedule', 'momoyoga-integration' );
        ?>
        <button id="insert-momoyoga-schedule" class="button" title="<?php echo( $title ); ?>" href="javascript:;">
            <span class="momoyoga-schedule-icon"></span> <?php echo( $title ); ?>
        </button>

        <?php
    }

    function handle_editor_view_js() {
        $current_screen = get_current_screen();

		if ( ! isset( $current_screen->id ) || $current_screen->base !== 'post' ) {
			return;
        }
        
		add_action( 'admin_print_footer_scripts', array( $this, 'editor_view_js_templates' ), 1 );
		wp_enqueue_script( 'momoyoga-editor-view', plugins_url( 'js/editor-view.js', __FILE__ ), array( 'wp-util', 'jquery', 'quicktags' ), MOMO_PLUGIN_VERSION, true );
		wp_localize_script( 'momoyoga-editor-view', 'momoyogaScheduleEditorView', array(
			'inline_editing_style' => plugins_url( 'css/editor-inline-editing-style.css?ver=' . MOMO_PLUGIN_VERSION, __FILE__ ),
			'labels'      => array(
                //Parse needed translation to the script
				'submit_button_text'  => __('Momoyoga schedule', 'momoyoga-integration')
			)
		) );
		add_editor_style( plugins_url( 'css/editor-style.css', __FILE__ ) );
    }

    function editor_view_js_templates() {
		?>
<script type="text/html" id="tmpl-momoyoga-schedule">
	<div class="card" class='contact-form commentsblock' onsubmit="return false;">
        {{{ data.body }}}
	</div>
</script>

<script type="text/html" id="tmpl-momoyoga-schedule-editor-inline">
    <h1 id="form-settings-header" class="grunion-section-header"><?php _e( 'Schedule settings', 'momoyoga-integration' ); ?></h1>
    <section class="card grunion-form-settings" aria-labelledby="form-settings-header">
        <label><?php _e( 'What is the URL of your Momoyoga schedule?', 'momoyoga-integration' ); ?>
            <input type="text" placeholder="<?php _e('https://www.momoyoga.com/example-yoga-studio', 'momoyoga-integration') ?>" name="schedule_url" value="{{ data.schedule_url }}" />
        </label>
    </section>
    <section class="buttons">
        <?php submit_button( __( 'Update settings', 'momoyoga-integration' ), 'primary', 'submit', false ); ?>
        <?php submit_button( __( 'Cancel', 'momoyoga-integration' ), 'delete', 'cancel', false ); ?>
    </section>
</script>
        <?php
    }
}
