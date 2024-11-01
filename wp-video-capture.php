<?php
/**
 * Plugin Name: Video Recorder
 * Plugin URI: https://vidrack.com
 * Description: Add a video camera to your website!
 * Version: 2.1.1
 * Author: Vidrack.com
 * License: GPLv2 or later
 *
 * @package wp-video-capture
 */

/**
 * Current plugin version.
 * Changes manually with every upgrade.
 */
define( 'VIDRACK_VERSION', '2.1.1' );

define( 'VIDRACK_FILENAME', __FILE__ );

define( 'VIDRACK_PLUGIN_BASENAME', plugin_basename( VIDRACK_FILENAME ) );

define( 'VIDRACK_DIR_URL', plugin_dir_url( VIDRACK_FILENAME ) );

define( 'VIDRACK_DIR_PATH', plugin_dir_path( VIDRACK_FILENAME ) );

define( 'VIDRACK_ASSETS_DIR_URL', VIDRACK_DIR_URL . 'assets/' );

$site_url = wp_parse_url( site_url() );
$hostname = $site_url['host'];
define( 'HOSTNAME', $hostname );

require_once VIDRACK_DIR_PATH . 'includes/class-wp-video-capture.php';
register_activation_hook( __FILE__, array( 'WP_Video_Capture', 'vidrack_activation' ) );