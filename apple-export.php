<?php
/**
 * Entry point for the plugin.
 *
 * This file is read by WordPress to generate the plugin information in the
 * admin panel.
 *
 * @link    http://beezwax.net
 * @since   0.2.0
 * @package WP_Plugin
 *
 * Plugin Name: Apple Export
 * Plugin URI:  http://beezwax.net
 * Description: Export and sync posts to Apple format.
 * Version:     0.9.0
 * Author:      Beezwax
 * Author URI:  http://beezwax.net
 * Text Domain: apple-export
 * Domain Path: lang/
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

// Plugin activation.
function activate_wp_plugin() {
	// Check for PHP version
	if ( version_compare( PHP_VERSION, '5.3.0' ) < 0 ) {
		deactivate_plugins( basename( __FILE__ ) );
		wp_die( __( 'This plugin requires at least PHP 5.3.0', 'apple-export' ) );
	}

	// Check for ZipArchive dependency
	if ( ! class_exists( 'ZipArchive' ) ) {
		deactivate_plugins( basename( __FILE__ ) );
		wp_die( __( 'This PHP installation was not compiled with ZipArchive, which is required by this plugin.', 'apple-export' ) );
	}
}

require plugin_dir_path( __FILE__ ) . 'includes/exporter/class-settings.php';

// Plugin deactivation. Clean up everything.
function deactivate_wp_plugin() {
	// Do something
	$settings = new Exporter\Settings;
	foreach ( $settings->all() as $name => $value ) {
		delete_option( $name );
	}
}

// WordPress VIP plugins do not execute these hooks, so ignore in that environment.
if ( ! defined( 'WPCOM_IS_VIP_ENV' ) || ! WPCOM_IS_VIP_ENV ) {
	register_activation_hook( __FILE__,   'activate_wp_plugin' );
	register_deactivation_hook( __FILE__, 'deactivate_wp_plugin' );
}

// Initiate plugin class
require plugin_dir_path( __FILE__ ) . 'includes/class-apple-export.php';
require plugin_dir_path( __FILE__ ) . 'admin/class-admin-apple-export.php';

new Admin_Apple_Export();
