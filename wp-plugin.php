<?php
/**
 * Entry point for the plugin.
 * 
 * This file is read by WordPress to generate the plugin information in the
 * admin panel.
 *
 * @link    http://beezwax.net
 * @since   0.0.0
 * @package WP_Plugin
 *
 * Plugin Name: Apple news feed
 */

if( ! defined( 'WPINC' ) )
    die;

// Plugin activation. Create tables and stuff.
function activate_wp_plugin() {
    // Do something
}

// Plugin deactivation. Clean up everything.
function deactivate_wp_plugin() {
    // Do something
}

register_activation_hook( __FILE__,   'activate_wp_plugin' );
register_deactivation_hook( __FILE__, 'deactivate_wp_plugin' );

// Initiate plugin class
require plugin_dir_path( __FILE__ ) . 'includes/class-apple-export.php';
require plugin_dir_path( __FILE__ ) . 'admin/class-admin-apple-export.php';
new Admin_Apple_Export();
