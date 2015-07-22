<?php
require_once plugin_dir_path( __FILE__ ) . '../includes/exporter/class-settings.php';
require_once plugin_dir_path( __FILE__ ) . 'settings/class-admin-settings-section.php';
require_once plugin_dir_path( __FILE__ ) . 'settings/class-admin-settings-section-api.php';
require_once plugin_dir_path( __FILE__ ) . 'settings/class-admin-settings-section-formatting.php';

use Exporter\Settings as Settings;

/**
 * This class is in charge of creating a WordPress page to manage the
 * Exporter's settings class.
 */
class Admin_Settings extends Apple_Export {

	/**
	 * Associative array of fields and types. If not present, defaults to string.
	 * Possible types are: integer, color, boolean, string and options.
	 * If options, use an array instead of a string.
	 *
	 * @since 0.4.0
	 */
	private $field_types;

	/**
	 * Optionally define more elaborated labels for each setting and store them
	 * here.
	 *
	 * @since 0.6.0
	 */
	private $field_labels;

	/**
	 * Only load settings once. Cache results for easy and efficient usage.
	 */
	private $loaded_settings;

	private $sections;
	private $page_name;

	function __construct() {
		$this->loaded_settings = null;
		$this->sections = array();
		$this->page_name = $this->plugin_domain . '-options';

		add_action( 'admin_init', array( $this, 'register_sections' ) );
		add_action( 'admin_menu', array( $this, 'setup_options_page' ) );
		add_action( 'admin_head', array( $this, 'settings_styles' ) );
	}

	public function settings_styles() {
		echo '<style type="text/css">';
		echo '.form-table.apple-export input[type=text],';
		echo '.form-table.apple-export input[type=number],';
		echo '.form-table.apple-export .select2,';
		echo '.form-table.apple-export input[type=password] { display: block; width: 285px; margin-bottom: 5px; }';

		echo '.form-table.apple-export select,';
		echo '.form-table.apple-export input[type=color] { display: inline-block; margin-right: 15px; }';
		echo '</style>';
	}

	private function add_sections() {
		$this->add_section( new Admin_Settings_Section_API( $this->page_name ) );
		$this->add_section( new Admin_Settings_Section_Formatting( $this->page_name ) );
	}

	private function add_section( $section ) {
		$this->sections[] = $section;
	}

	/**
	 * Load exporter settings and register them.
	 *
	 * @since 0.4.0
	 */
	public function register_sections() {
		$this->add_sections();
		foreach ( $this->sections as $section ) {
			$section->register();
		}
	}

	/**
	 * Options page setup
	 */
	public function setup_options_page() {
		$this->register_assets();

		add_options_page(
			'Apple News Options',
			'Apple News',
			'manage_options',
			$this->page_name,
			array( $this, 'page_options_render' )
		);
	}

	public function page_options_render() {
		if ( ! current_user_can( 'manage_options' ) )
			wp_die( __( 'You do not have permissions to access this page.' ) );

		$sections = $this->sections;
		include plugin_dir_path( __FILE__ ) . 'partials/page_options.php';
	}

	private function register_assets() {
		wp_enqueue_style( 'apple-export-select2-css', plugin_dir_url( __FILE__ ) .
			'../vendor/select2/select2.min.css', array() );

		wp_enqueue_script( 'apple-export-select2-js', plugin_dir_url( __FILE__ ) .
			'../vendor/select2/select2.full.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'apple-export-settings-js', plugin_dir_url( __FILE__ ) .
			'../assets/js/settings.js', array( 'jquery', 'apple-export-select2-js' )
		);
	}

	/**
	 * Creates a new \Exporter\Settings instance and loads it with WordPress' saved
	 * settings.
	 */
	public function fetch_settings() {
		if ( is_null( $this->loaded_settings ) ) {
			$settings = new Settings();
			foreach ( $settings->all() as $key => $value ) {
				$wp_value = esc_attr( get_option( $key ) ) ?: $value;
				$settings->set( $key, $wp_value );
			}
			$this->loaded_settings = $settings;
		}

		return $this->loaded_settings;
	}

}
