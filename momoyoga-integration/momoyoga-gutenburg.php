<?php

class GutenbergBlock
{
	public function add_hooks($momoyogaSchedulePlugin) {
		wp_register_script(
			'momoyoga-integration-schedule',
			plugins_url( 'js/block-schedule.js', __FILE__ ),
			array( 'wp-blocks', 'wp-editor', 'wp-element', 'wp-i18n' ),
			MOMO_PLUGIN_VERSION
		);

		wp_register_style('momoyoga-integration-editor', plugins_url( 'css/block-editor.css', __FILE__ ), array( 'wp-edit-blocks' ), MOMO_PLUGIN_VERSION);

		wp_set_script_translations('momoyoga-integration-schedule', 'momoyoga-integration', plugin_dir_path( __FILE__ ) . 'lang');

		register_block_type( 'momoyoga-integration/schedule', array(
			'editor_script'   => 'momoyoga-integration-schedule',
			'editor_style'    => 'momoyoga-integration-editor',
			'render_callback' => array($momoyogaSchedulePlugin, 'parse_momoyoga_schedule')
		) );
	}
}
