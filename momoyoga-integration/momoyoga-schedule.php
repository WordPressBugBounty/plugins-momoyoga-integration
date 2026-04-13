<?php
/**
 * Plugin Name:       Yoga Schedule Momoyoga
 * Plugin URI:        https://www.momoyoga.com
 * Description:       Display your Momoyoga schedule directly on your WordPress website.
 * Version:           3.0.0
 * Requires at least: 6.5
 * Requires PHP:      7.4
 * Author:            Momoyoga
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       momoyoga-integration
 *
 * @package           momoyoga-integration
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

const MOMO_SHORTCODE = 'momoyoga-schedule';

/**
 * Enqueue Momoyoga schedule assets (safe to call multiple times).
 */
function momoyoga_integration_schedule_enqueue_assets(): void
{
    wp_enqueue_style('momoyoga-integration/schedule-style');

    // WP 6.5+ (plugin requires at least 6.5): ESM module loader.
    wp_enqueue_script_module('momoyoga-integration/schedule-script');
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 */
function momoyoga_integration_schedule_init()
{
    add_shortcode(MOMO_SHORTCODE, 'momoyoga_integration_schedule_shortcode');

    // Static block registration (no render_callback).
    register_block_type_from_metadata(__DIR__ . '/build/block');
}

/**
 * Register frontend assets and load them if the current post contains the shortcode or block. This ensures assets are only loaded when needed.
 */
function momoyoga_integration_schedule_frontend_assets(): void
{
    wp_register_script_module(
        'momoyoga-integration/schedule-script',
        'https://cdn.momo.yoga/plugins/schedule-integration/schedule.js',
        [],
        null,
    );

    wp_register_style(
        'momoyoga-integration/schedule-style',
        'https://cdn.momo.yoga/plugins/schedule-integration/schedule.css',
        [],
        null,
    );

    global $post;

    $includeAssets = is_a( $post, 'WP_Post' )
        && (
            has_shortcode( $post->post_content, MOMO_SHORTCODE)
            || has_block('momoyoga-integration/schedule', $post)
        );

    if (!is_admin() && !$includeAssets) {
        return;
    }

    momoyoga_integration_schedule_enqueue_assets();
}

function momoyoga_integration_schedule_shortcode(array $attrs = [])
{
    // Ensure frontend assets load whenever shortcode renders (also covers non-content contexts).
    if (!is_admin()) {
        momoyoga_integration_schedule_enqueue_assets();
    }

    if (array_key_exists('schedule_url', $attrs)) {
        $scheduleUrl = $attrs['schedule_url'];
    } else {
        $scheduleUrl = null;
    }

    if (empty($scheduleUrl)) {
        return '<div>' . esc_html__('No schedule URL defined.', 'momoyoga-integration') . '</div>';
    }

    $filteredUrl = filter_var($scheduleUrl, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED);

    if (false === $filteredUrl) {
        return '<div>' . esc_html__('Schedule URL invalid.', 'momoyoga-integration') . '</div>';
    }

    return '<div data-momo-schedule="' . esc_attr(sanitize_url($filteredUrl)) . '"></div>';
}

// Load in editor/admin when adding/using the block.
add_action('enqueue_block_editor_assets', 'momoyoga_integration_schedule_enqueue_assets');

// Frontend: enqueue only when needed.
add_action('wp_enqueue_scripts', 'momoyoga_integration_schedule_frontend_assets');

add_action('init', 'momoyoga_integration_schedule_init');
