<?php
/**
 * Database operations for Scripts & Styles Lite Tweaks
 *
 * @package ScriptsStylesLiteTweaks
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles all database operations
 */
class SSLT_Database {

	/**
	 * Settings cache
	 *
	 * @var array|null
	 */
	private $settings_cache = null;

	/**
	 * Get table name with prefix
	 *
	 * @return string
	 */
	private function get_table_name() {
		global $wpdb;
		return $wpdb->prefix . SSLT_TABLE_SETTINGS;
	}

	/**
	 * Create database table on activation
	 */
	public static function activate() {
		global $wpdb;
		
		$table_name = $wpdb->prefix . SSLT_TABLE_SETTINGS;
		$charset_collate = $wpdb->get_charset_collate();
		
		// Create table using %i placeholder for WordPress 6.2+
		$sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			setting_key varchar(191) NOT NULL,
			setting_value longtext,
			PRIMARY KEY (id),
			UNIQUE KEY setting_key (setting_key)
		) {$charset_collate};";
		
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
		
		// Verify table was created
		$table_exists = $wpdb->get_var( $wpdb->prepare(
			"SHOW TABLES LIKE %s",
			$table_name
		) );
		
		if ( $table_name !== $table_exists ) {
			error_log( 'SSLT: Failed to create database table ' . $table_name );
			return;
		}
		
		// Set default settings
		$defaults = array(
			'disable_jquery_migrate' => '0',
			'disable_emoji_scripts' => '0',
			'disable_embeds' => '0',
			'disable_admin_bar_scripts' => '0',
			'disable_dashicons' => '0',
			'enable_selective_blocks' => '0',
			'disable_global_styles' => '0',
			'disable_classic_theme_styles' => '0',
			'disable_recent_comments_style' => '0',
			'cleanup_on_uninstall' => '0',
		);
		
		$database = new self();
		foreach ( $defaults as $key => $value ) {
			// Only set if doesn't already exist
			$existing = $database->get_setting( $key );
			if ( false === $existing ) {
				$result = $database->save_setting( $key, $value );
				if ( false === $result ) {
					error_log( 'SSLT: Failed to set default for ' . $key );
				}
			}
		}
	}

	/**
	 * Deactivation cleanup
	 */
	public static function deactivate() {
		// Clear any transients
		global $wpdb;
		
		$wpdb->query( $wpdb->prepare(
			"DELETE FROM {$wpdb->options} 
			WHERE option_name LIKE %s 
			OR option_name LIKE %s",
			$wpdb->esc_like( '_transient_sslt_' ) . '%',
			$wpdb->esc_like( '_transient_timeout_sslt_' ) . '%'
		) );
	}

	/**
	 * Get setting value
	 *
	 * @param string $key Setting key
	 * @param mixed  $default Default value
	 * @return mixed Setting value or default
	 */
	public function get_setting( $key, $default = false ) {
		global $wpdb;
		
		$table = $this->get_table_name();
		
		$value = $wpdb->get_var( $wpdb->prepare(
			"SELECT setting_value FROM `{$table}` WHERE setting_key = %s",
			$key
		) );
		
		if ( null === $value ) {
			return $default;
		}
		
		return maybe_unserialize( $value );
	}

	/**
	 * Get all settings
	 *
	 * @return array All settings
	 */
	public function get_all_settings() {
		if ( null !== $this->settings_cache ) {
			return $this->settings_cache;
		}
		
		global $wpdb;
		$table = $this->get_table_name();
		
		$results = $wpdb->get_results(
			"SELECT setting_key, setting_value FROM `{$table}`",
			ARRAY_A
		);
		
		if ( false === $results || null === $results ) {
			error_log( 'SSLT: Failed to retrieve settings - ' . $wpdb->last_error );
			return array();
		}
		
		$settings = array();
		if ( is_array( $results ) ) {
			foreach ( $results as $row ) {
				if ( isset( $row['setting_key'] ) && isset( $row['setting_value'] ) ) {
					$settings[ $row['setting_key'] ] = maybe_unserialize( $row['setting_value'] );
				}
			}
		}
		
		$this->settings_cache = $settings;
		return $settings;
	}

	/**
	 * Save setting value
	 *
	 * @param string $key Setting key
	 * @param mixed  $value Setting value
	 * @return bool Success
	 */
	public function save_setting( $key, $value ) {
		global $wpdb;
		
		$table = $this->get_table_name();
		$result = $wpdb->replace(
			$table,
			array(
				'setting_key' => $key,
				'setting_value' => maybe_serialize( $value ),
			),
			array( '%s', '%s' )
		);
		
		if ( false === $result ) {
			error_log( 'SSLT DB Error: ' . $wpdb->last_error );
			return false;
		}
		
		// Clear cache
		$this->settings_cache = null;
		
		return true;
	}

	/**
	 * Delete setting
	 *
	 * @param string $key Setting key
	 * @return bool Success
	 */
	public function delete_setting( $key ) {
		global $wpdb;
		
		$table = $this->get_table_name();
		$result = $wpdb->delete(
			$table,
			array( 'setting_key' => $key ),
			array( '%s' )
		);
		
		if ( false === $result ) {
			error_log( 'SSLT DB Error: ' . $wpdb->last_error );
			return false;
		}
		
		// Clear cache
		$this->settings_cache = null;
		
		return true;
	}
}