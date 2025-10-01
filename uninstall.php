<?php
/**
 * Uninstall handler for Scripts & Styles Lite Tweaks
 *
 * @package ScriptsStylesLiteTweaks
 * @since 1.0.0
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Get cleanup preference from custom table
global $wpdb;

$table_name = $wpdb->prefix . 'sslt_settings';

$cleanup = $wpdb->get_var( $wpdb->prepare(
	"SELECT setting_value FROM %i WHERE setting_key = %s",
	$table_name,
	'cleanup_on_uninstall'
) );

if ( '1' === $cleanup ) {
	// Drop custom table
	$wpdb->query( $wpdb->prepare( "DROP TABLE IF EXISTS %i", $table_name ) );
	
	// Clean transients
	$wpdb->query( $wpdb->prepare(
		"DELETE FROM {$wpdb->options} 
		WHERE option_name LIKE %s 
		OR option_name LIKE %s",
		$wpdb->esc_like( '_transient_sslt_' ) . '%',
		$wpdb->esc_like( '_transient_timeout_sslt_' ) . '%'
	) );
	
	// Clear object cache
	wp_cache_flush();
}
