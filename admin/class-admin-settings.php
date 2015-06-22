<?php
require_once plugin_dir_path( __FILE__ ) . '../includes/exporter/class-settings.php';

use Exporter\Settings as Settings;

class Admin_Settings {

	/**
	 * Associative array of fields and types. If not present, defaults to string.
	 * Possible types are: integer, color, boolean, string and options.
	 * If options, use an array instead of a string.
	 *
	 * @since 0.4.0
	 */
	private $field_types = array(
		'body_size' => 'integer',
		'body_color' => 'color',
		'body_link_color' => 'color',
		'initial_dropcap' => 'boolean', // boolean is internally a 'yes' or 'no' string
		'dropcap_color' => 'color',
		'header_color' => 'color',
		'header1_size' => 'integer',
		'header2_size' => 'integer',
		'header3_size' => 'integer',
		'header4_size' => 'integer',
		'header5_size' => 'integer',
		'header6_size' => 'integer',
		'pullquote_size' => 'integer',
		'pullquote_color' => 'color',
		'pullquote_transform' => array( 'none', 'uppercase' ),
		'gallery_type' => array( 'gallery', 'mosaic' ),
	);

	function __construct() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'setup_options_page' ) );
	}

	public function render_field( $args ) {
		list( $name, $default_value ) = $args;
		$type  = $this->get_type_for_field( $name );
		$value = esc_attr( get_option( $name ) ) ?: $default_value;
		$field = null;

		// FIXME: A cleaner object-oriented solution would create Input objects
		// and instantiate them according to their type.
		if ( is_array( $type ) ) {
			$field = '<select name="%s">';
			foreach ( $type as $option ) {
				$field .= "<option value='$option'";
				if ( $option == $value ) {
					$field .= ' selected ';
				}
				$field .= ">$option</option>";
			}
			$field .= '</select>';
		} else if ( 'boolean' == $type ) {
			$field = '<select name="%s">';

			$field .= '<option value="yes"';
			if ( 'yes' == $value ) {
				$field .= ' selected ';
			}
			$field .= '>Yes</option>';

			$field .= '<option value="no"';
			if ( 'yes' != $value ) {
				$field .= ' selected ';
			}
			$field .= '>No</option>';

			$field .= '</select>';
		} else if ( 'string' == $type ) {
			$field = '<input type="text" name="%s" value="%s">';
		} else if ( 'integer' == $type ) {
			$field = '<input type="number" name="%s" value="%s">';
		} else if ( 'color' == $type ) {
			$field = '<input type="color" name="%s" value="%s">';
		}

		printf( $field, $name, $value );
	}

	public function print_general_settings_section_info() {
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
			array( $this, 'print_general_settings_section_info' ),
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

	private function get_type_for_field( $name ) {
		if ( array_key_exists( $name, $this->field_types ) ) {
			return $this->field_types[ $name ];
		}

		return 'string';
	}

}
