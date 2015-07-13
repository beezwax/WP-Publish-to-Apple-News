<?php
/**
 * Entry point for the admin side of the WP Plugin.
 *
 * @author  Federico Ramirez
 * @since   0.0.0
 */

require_once plugin_dir_path( __FILE__ ) . 'class-admin-settings.php';
require_once plugin_dir_path( __FILE__ ) . 'class-admin-post-sync.php';
require_once plugin_dir_path( __FILE__ ) . 'class-admin-page-index.php';

/**
 * Main class for the plugin.
 * FIXME: The admin page does too much, split into several classes.
 */
class Admin_Apple_Export extends Apple_Export {

	function __construct() {
		// This is required to download files and setting headers.
		ob_start();

		// Register hooks
		add_action( 'admin_head', array( $this, 'plugin_styles' ) );

		// Admin_Settings builds the settings page for the plugin. Besides setting
		// it up, let's get the settings getter and setter object and save it into
		// $settings.
		$admin_settings = new Admin_Settings;
		$settings       = $admin_settings->fetch_settings();

		// Set up index page
		new Admin_Page_Index( $settings );

		// Set up posts syncing if enabled in the settings
		if ( 'yes' == $settings->get( 'api_autosync' ) ) {
			new Admin_Post_Sync( $settings );
		}
	}

	public function plugin_styles() {
		$page = ( isset( $_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : null;

		if ( $this->plugin_name . '_index' != $page ) {
			return;
		}

		// Styles are tiny, for now just embed them.
		echo '<style type="text/css">';
		echo '.wp-list-table .column-sync { width: 15%; }';
		echo '</style>';
	}

}
