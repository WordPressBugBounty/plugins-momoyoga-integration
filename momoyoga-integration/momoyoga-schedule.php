<?php
/**
 * @package Momoyoga integration plugin
 * @version 2.8.2
 */
/*
Plugin Name:    Yoga Schedule Momoyoga
Plugin URI:     https://help.momoyoga.com/hc/en-us/articles/115003513171-How-to-integrate-the-schedule-with-my-website-using-Wordpress-
Description:    Show your Momoyoga class schedule on your WordPress website.
Version:        2.8.2
Author:         Momoyoga
Author URI:     https://www.momoyoga.com/en/
License:        GPL2
License URI:    https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:    momoyoga-integration
Domain Path:    /lang
*/

define('MOMO_PLUGIN_VERSION', '2.8.2');

require_once plugin_dir_path( __FILE__ ) . 'momoyoga-admin.php';
require_once plugin_dir_path( __FILE__ ) . 'momoyoga-gutenburg.php';

class MomoyogaSchedulePlugin
{
	public function add_hooks()
	{
		add_shortcode( 'momoyoga-schedule', array( $this, 'parse_momoyoga_schedule' ));

		wp_enqueue_script( 'momoyoga_schedule', plugins_url( 'js/schedule.min.js', __FILE__ ), array( 'jquery' ), MOMO_PLUGIN_VERSION, true );
		wp_enqueue_style( 'momoyoga_frontend', plugins_url( 'css/schedule-frontend.min.css', __FILE__), array (), MOMO_PLUGIN_VERSION );

		load_plugin_textdomain( 'momoyoga-integration', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
	}

	function parse_momoyoga_schedule ($attrs)
	{
		$momoyoga_settings = get_option('momoyoga-settings');

		if (is_array($attrs) && array_key_exists('schedule_url', $attrs)) {
			$schedule_url = $attrs['schedule_url'];
		} else if ($momoyoga_settings['momoyoga_schedule_url'] !== null) {
			$schedule_url = $momoyoga_settings['momoyoga_schedule_url'];
		} else {
			$schedule_url = null;
		}

		if (false === filter_var($schedule_url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
			return '<div class="momoyoga-schedule">'.__('No schedule URL defined.','momoyoga-integration').'</div>';
		}

		if ($schedule_url === null || empty($schedule_url)) {
			return '<div class="momoyoga-schedule">'.__('Schedule URL invalid.','momoyoga-integration').'</div>';
		}

		return '<div class="momoyoga-schedule" data-momo-schedule="' . $schedule_url .'"></div>';
	}
}

$momoyogaSchedulePlugin = new MomoyogaSchedulePlugin();
add_action( 'init', array( $momoyogaSchedulePlugin, 'add_hooks' ) );

if ( is_admin() ) {
	$editor_button = new MomoyogaEditorButton();
	add_action( 'init', array( $editor_button, 'add_hooks' ) );

	if (function_exists('register_block_type')) {
		$gutenberg_block = new GutenbergBlock($momoyogaSchedulePlugin);
		add_action( 'init', array( $gutenberg_block, 'add_hooks' ) );
	}
}
