<?php
require_once plugin_dir_path( __FILE__ ) . '../includes/exporter/class-settings.php';

use Exporter\Settings as Settings;

class Admin_Settings {

	function __construct() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'setup_options_page' ) );
	}

	public function render_field( $args ) {
		list( $name, $default_value ) = $args;
		printf(
			'<input type="text" id="title" name="%s", value="%s">',
			$name,
			get_option( $name ) ? esc_attr( get_option( $name ) ) : $default_value
		);
	}

	public function print_section_info() {
		echo 'Settings which apply globally to the plugin functionality.';
	}


	/**
	 * Load exporter settings and register them.
	 *
	 * @since 0.4.0
	 */
	public function register_settings() {
		add_settings_section(
			'apple-export-options-section-general', // ID
			'General Settings', // Title
			array( $this, 'print_section_info' ),
			'apple-export-options'
	 	);

		$settings = new Settings();
		foreach ( $settings->all() as $name => $value ) {
			register_setting( 'apple-export-options', $name );
			add_settings_field(
				$name,                                     // ID
				ucfirst( str_replace( '_', ' ', $name ) ), // Title
				array( $this, 'render_field' ),            // Render calback
				'apple-export-options',                    // Page
				'apple-export-options-section-general',    // Section
				array( $name, $value )                     // Args passed to the render callback
		 	);
		}
	}

	/**
	 * Options page setup
	 */
	public function setup_options_page() {
		add_options_page(
			'Apple Export Options',
			'Apple Export',
			'manage_options',
			'apple-export-options',
			array( $this, 'page_options_render' )
		);
	}

	public function page_options_render() {
		if ( ! current_user_can( 'manage_options' ) )
			wp_die( __( 'You do not have permissions to access this page.' ) );

		include plugin_dir_path( __FILE__ ) . 'partials/page_options.php';
	}

}
