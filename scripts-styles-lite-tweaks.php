<?php
/**
 * Plugin Name: Scripts & Styles Lite Tweaks
 * Description: Performance optimization plugin for selectively disabling unnecessary scripts and styles
 * Version: 1.0.0
 * Text Domain: scripts-styles-lite-tweaks
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define constants
define( 'SSLT_VERSION', '1.0.0' );
define( 'SSLT_DIR', plugin_dir_path( __FILE__ ) );
define( 'SSLT_URL', plugin_dir_url( __FILE__ ) );
define( 'SSLT_TABLE_SETTINGS', 'sslt_settings' );

// Include classes
require_once SSLT_DIR . 'includes/class-database.php';
require_once SSLT_DIR . 'includes/class-core.php';
require_once SSLT_DIR . 'includes/class-admin.php';

/**
 * Initialize plugin
 */
function sslt_init() {
	$database = new SSLT_Database();
	$core = new SSLT_Core( $database );
	
	if ( is_admin() ) {
		new SSLT_Admin( $database );
	}
}
add_action( 'plugins_loaded', 'sslt_init' );

/**
 * Activation hook
 */
register_activation_hook( __FILE__, array( 'SSLT_Database', 'activate' ) );

/**
 * Deactivation hook
 */
register_deactivation_hook( __FILE__, array( 'SSLT_Database', 'deactivate' ) );
