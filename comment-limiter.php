<?php
/*
Plugin Name: Comment Limiter
Description: Limit the minimum and maximum number of characters allowed in post comments.
Version:     2.4.0
Author:      Anass Rahou
License:     GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: comment-limiter
Domain Path: /languages
*/

defined( 'ABSPATH' ) || exit;


if( ! defined( 'CL_VERSION' ) ) {
  define( 'CL_VERSION', '2.4.0' );
}

if ( ! defined( 'CL_PLUGIN_PATH' ) ) {
  define( 'CL_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}

$files = [
    'includes/class-comment-limiter-i18n.php',
    'includes/class-comment-limiter-config.php',
    'includes/class-comment-limiter-settings.php'
];

foreach ( $files as $file ) {
    $path = CL_PLUGIN_PATH . $file;
    if ( file_exists( $path ) ) {
        require_once $path;
    }
}


Comment_Limiter_i18n::factory();
Comment_Limiter_Config::factory();
Comment_Limiter_Settings::factory();


/**
 * Add settings link to plugin actions
 *
 * @param  array  $plugin_actions
 * @param  string $plugin_file
 * @since  1.0
 * @return array
 */
function cl_filter_plugin_action_links( $plugin_actions ) {
    $plugin_actions['cl_settings'] = sprintf(
        '<a href="%s">%s</a>',
        esc_url( admin_url( 'edit-comments.php?page=comment-limiter' ) ),
        esc_html__( 'Settings', 'comment-limiter' )
    );

    return $plugin_actions;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'cl_filter_plugin_action_links' );


