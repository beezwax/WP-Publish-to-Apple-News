<?php
/**
 * Entry point for the admin side of the WP Plugin.
 *
 * @author  Federico Ramirez
 * @since   0.0.0
 */

require_once plugin_dir_path( __FILE__ ) . 'class-admin-settings.php';
require_once plugin_dir_path( __FILE__ ) . 'class-admin-post-sync.php';
require_once plugin_dir_path( __FILE__ ) . 'class-admin-index-page.php';
require_once plugin_dir_path( __FILE__ ) . 'class-admin-bulk-export-page.php';
require_once plugin_dir_path( __FILE__ ) . 'class-admin-flash.php';

/**
 * Entry-point class for the plugin.
 */
class Admin_Apple_Export extends Apple_Export {

	function __construct() {
		// This is required to download files and setting headers.
		ob_start();

		// Initialize flash messaging utility
		new Flash;

		// Register hooks
		add_action( 'admin_head', array( $this, 'plugin_styles' ) );

		// Admin_Settings builds the settings page for the plugin. Besides setting
		// it up, let's get the settings getter and setter object and save it into
		// $settings.
		$admin_settings = new Admin_Settings;
		$settings       = $admin_settings->fetch_settings();

		// Set up main page
		new Admin_Index_Page( $settings );
		// Set up all sub pages
		new Admin_Bulk_Export_Page( $settings );
		// Set up posts syncing if enabled in the settings
		new Admin_Post_Sync( $settings );
	}

	public function plugin_styles() {
		$page = ( isset( $_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : null;

		if ( $this->plugin_slug . '_index' != $page ) {
			return;
		}

		// Styles are tiny, for now just embed them.
		echo '<style type="text/css">';
		echo '.wp-list-table .column-sync { width: 15%; }';
		echo '.wp-list-table .column-updated_at { width: 15%; }';
		// Clipboard fix
		echo '.row-actions .zeroclipboard-is-hover { color: #00a0d2; }';
		echo '.row-actions.is-active { visibility: visible }';
		echo '</style>';
	}

}
