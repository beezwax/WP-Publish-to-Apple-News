<?php
require_once plugin_dir_path( __FILE__ ) . '../includes/exporter/class-settings.php';

use Exporter\Settings as Settings;

/**
 * This class is in charge of creating a WordPress page to manage the
 * Exporter's settings class.
 */
class Admin_Settings {

	/**
	 * All available iOS fonts.
	 *
	 * @since 0.4.0
	 */
	private $fonts = array(
		'AcademyEngravedLetPlain',
		'AlNile-Bold',
		'AlNile',
		'AmericanTypewriter',
		'AmericanTypewriter-Bold',
		'AmericanTypewriter-Condensed',
		'AmericanTypewriter-CondensedBold',
		'AmericanTypewriter-CondensedLight',
		'AmericanTypewriter-Light',
		'AppleColorEmoji',
		'AppleSDGothicNeo-Thin',
		'AppleSDGothicNeo-Light',
		'AppleSDGothicNeo-Regular',
		'AppleSDGothicNeo-Medium',
		'AppleSDGothicNeo-SemiBold',
		'AppleSDGothicNeo-Bold',
		'AppleSDGothicNeo-Medium',
		'ArialMT',
		'Arial-BoldItalicMT',
		'Arial-BoldMT',
		'Arial-ItalicMT',
		'ArialHebrew',
		'ArialHebrew-Bold',
		'ArialHebrew-Light',
		'ArialRoundedMTBold',
		'Avenir-Black',
		'Avenir-BlackOblique',
		'Avenir-Book',
		'Avenir-BookOblique',
		'Avenir-Heavy',
		'Avenir-HeavyOblique',
		'Avenir-Light',
		'Avenir-LightOblique',
		'Avenir-Medium',
		'Avenir-MediumOblique',
		'Avenir-Oblique',
		'Avenir-Roman',
		'AvenirNext-Bold',
		'AvenirNext-BoldItalic',
		'AvenirNext-DemiBold',
		'AvenirNext-DemiBoldItalic',
		'AvenirNext-Heavy',
		'AvenirNext-HeavyItalic',
		'AvenirNext-Italic',
		'AvenirNext-Medium',
		'AvenirNext-MediumItalic',
		'AvenirNext-Regular',
		'AvenirNext-UltraLight',
		'AvenirNext-UltraLightItalic',
		'AvenirNext-Bold',
		'AvenirNext-BoldItalic',
		'AvenirNext-DemiBold',
		'AvenirNext-DemiBoldItalic',
		'AvenirNext-Heavy',
		'AvenirNext-HeavyItalic',
		'AvenirNext-Italic',
		'AvenirNext-Medium',
		'AvenirNext-MediumItalic',
		'AvenirNext-Regular',
		'AvenirNext-UltraLight',
		'AvenirNext-UltraLightItalic',
		'BanglaSangamMN',
		'BanglaSangamMN-Bold',
		'Baskerville',
		'Baskerville-Bold',
		'Baskerville-BoldItalic',
		'Baskerville-Italic',
		'Baskerville-SemiBold',
		'Baskerville-SemiBoldItalic',
		'BodoniSvtyTwoITCTT-Bold',
		'BodoniSvtyTwoITCTT-Book',
		'BodoniSvtyTwoITCTT-BookIta',
		'BodoniSvtyTwoOSITCTT-Bold',
		'BodoniSvtyTwoOSITCTT-Book',
		'BodoniSvtyTwoOSITCTT-BookIt',
		'BodoniSvtyTwoSCITCTT-Book',
		'BradleyHandITCTT-Bold',
		'ChalkboardSE-Bold',
		'ChalkboardSE-Light',
		'ChalkboardSE-Regular',
		'Chalkduster',
		'Cochin',
		'Cochin-Bold',
		'Cochin-BoldItalic',
		'Cochin-Italic',
		'Copperplate',
		'Copperplate-Bold',
		'Copperplate-Light',
		'Courier',
		'Courier-Bold',
		'Courier-BoldOblique',
		'Courier-Oblique',
		'CourierNewPS-BoldItalicMT',
		'CourierNewPS-BoldMT',
		'CourierNewPS-ItalicMT',
		'CourierNewPSMT',
		'DBLCDTempBlack',
		'DINAlternate-Bold',
		'DINCondensed-Bold',
		'DamascusBold',
		'Damascus',
		'DamascusLight',
		'DamascusMedium',
		'DamascusSemiBold',
		'DevanagariSangamMN',
		'DevanagariSangamMN-Bold',
		'Didot',
		'Didot-Bold',
		'Didot-Italic',
		'DiwanMishafi',
		'EuphemiaUCAS',
		'EuphemiaUCAS-Bold',
		'EuphemiaUCAS-Italic',
		'Farah',
		'Futura-CondensedExtraBold',
		'Futura-CondensedMedium',
		'Futura-Medium',
		'Futura-MediumItalic',
		'GeezaPro',
		'GeezaPro-Bold',
		'Georgia',
		'Georgia-Bold',
		'Georgia-BoldItalic',
		'Georgia-Italic',
		'GillSans',
		'GillSans-Bold',
		'GillSans-BoldItalic',
		'GillSans-Italic',
		'GillSans-Light',
		'GillSans-LightItalic',
		'GujaratiSangamMN',
		'GujaratiSangamMN-Bold',
		'GurmukhiMN',
		'GurmukhiMN-Bold',
		'STHeitiSC-Light',
		'STHeitiSC-Medium',
		'STHeitiTC-Light',
		'STHeitiTC-Medium',
		'Helvetica',
		'Helvetica-Bold',
		'Helvetica-BoldOblique',
		'Helvetica-Light',
		'Helvetica-LightOblique',
		'Helvetica-Oblique',
		'HelveticaNeue',
		'HelveticaNeue-Bold',
		'HelveticaNeue-BoldItalic',
		'HelveticaNeue-CondensedBlack',
		'HelveticaNeue-CondensedBold',
		'HelveticaNeue-Italic',
		'HelveticaNeue-Light',
		'HelveticaNeue-LightItalic',
		'HelveticaNeue-Medium',
		'HelveticaNeue-MediumItalic',
		'HelveticaNeue-UltraLight',
		'HelveticaNeue-UltraLightItalic',
		'HelveticaNeue-Thin',
		'HelveticaNeue-ThinItalic',
		'HiraKakuProN-W3',
		'HiraKakuProN-W6',
		'HiraMinProN-W3',
		'HiraMinProN-W6',
		'HoeflerText-Black',
		'HoeflerText-BlackItalic',
		'HoeflerText-Italic',
		'HoeflerText-Regular',
		'IowanOldStyle-Bold',
		'IowanOldStyle-BoldItalic',
		'IowanOldStyle-Italic',
		'IowanOldStyle-Roman',
		'Kailasa',
		'Kailasa-Bold',
		'KannadaSangamMN',
		'KannadaSangamMN-Bold',
		'KhmerSangamMN',
		'KohinoorDevanagari-Book',
		'KohinoorDevanagari-Light',
		'KohinoorDevanagari-Medium',
		'LaoSangamMN',
		'MalayalamSangamMN',
		'MalayalamSangamMN-Bold',
		'Marion-Bold',
		'Marion-Italic',
		'Marion-Regular',
		'Menlo-BoldItalic',
		'Menlo-Regular',
		'Menlo-Bold',
		'Menlo-Italic',
		'MarkerFelt-Thin',
		'MarkerFelt-Wide',
		'Noteworthy-Bold',
		'Noteworthy-Light',
		'Optima-Bold',
		'Optima-BoldItalic',
		'Optima-ExtraBlack',
		'Optima-Italic',
		'Optima-Regular',
		'OriyaSangamMN',
		'OriyaSangamMN-Bold',
		'Palatino-Bold',
		'Palatino-BoldItalic',
		'Palatino-Italic',
		'Palatino-Roman',
		'Papyrus',
		'Papyrus-Condensed',
		'PartyLetPlain',
		'SanFranciscoDisplay-Black',
		'SanFranciscoDisplay-Bold',
		'SanFranciscoDisplay-Heavy',
		'SanFranciscoDisplay-Light',
		'SanFranciscoDisplay-Medium',
		'SanFranciscoDisplay-Regular',
		'SanFranciscoDisplay-Semibold',
		'SanFranciscoDisplay-Thin',
		'SanFranciscoDisplay-Ultralight',
		'SanFranciscoRounded-Black',
		'SanFranciscoRounded-Bold',
		'SanFranciscoRounded-Heavy',
		'SanFranciscoRounded-Light',
		'SanFranciscoRounded-Medium',
		'SanFranciscoRounded-Regular',
		'SanFranciscoRounded-Semibold',
		'SanFranciscoRounded-Thin',
		'SanFranciscoRounded-Ultralight',
		'SanFranciscoText-Bold',
		'SanFranciscoText-BoldG1',
		'SanFranciscoText-BoldG2',
		'SanFranciscoText-BoldG3',
		'SanFranciscoText-BoldItalic',
		'SanFranciscoText-BoldItalicG1',
		'SanFranciscoText-BoldItalicG2',
		'SanFranciscoText-BoldItalicG3',
		'SanFranciscoText-Heavy',
		'SanFranciscoText-HeavyItalic',
		'SanFranciscoText-Light',
		'SanFranciscoText-LightItalic',
		'SanFranciscoText-Medium',
		'SanFranciscoText-MediumItalic',
		'SanFranciscoText-Regular',
		'SanFranciscoText-RegularG1',
		'SanFranciscoText-RegularG2',
		'SanFranciscoText-RegularG3',
		'SanFranciscoText-RegularItalic',
		'SanFranciscoText-RegularItalicG1',
		'SanFranciscoText-RegularItalicG2',
		'SanFranciscoText-RegularItalicG3',
		'SanFranciscoText-Semibold',
		'SanFranciscoText-SemiboldItalic',
		'SanFranciscoText-Thin',
		'SanFranciscoText-ThinItalic',
		'SavoyeLetPlain',
		'SinhalaSangamMN',
		'SinhalaSangamMN-Bold',
		'SnellRoundhand',
		'SnellRoundhand-Black',
		'SnellRoundhand-Bold',
		'Superclarendon-Regular',
		'Superclarendon-BoldItalic',
		'Superclarendon-Light',
		'Superclarendon-BlackItalic',
		'Superclarendon-Italic',
		'Superclarendon-LightItalic',
		'Superclarendon-Bold',
		'Superclarendon-Black',
		'Symbol',
		'TamilSangamMN',
		'TamilSangamMN-Bold',
		'TeluguSangamMN',
		'TeluguSangamMN-Bold',
		'Thonburi',
		'Thonburi-Bold',
		'Thonburi-Light',
		'TimesNewRomanPS-BoldItalicMT',
		'TimesNewRomanPS-BoldMT',
		'TimesNewRomanPS-ItalicMT',
		'TimesNewRomanPSMT',
		'Trebuchet-BoldItalic',
		'TrebuchetMS',
		'TrebuchetMS-Bold',
		'TrebuchetMS-Italic',
		'Verdana',
		'Verdana-Bold',
		'Verdana-BoldItalic',
		'Verdana-Italic',
		'ZapfDingbatsITC',
		'Zapfino',
	);

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

	function __construct() {
		$this->field_types = array(
			'api_secret'           => 'password',
			'api_autosync'         => array('yes', 'no'),
			'layout_margin'        => 'integer',
			'layout_gutter'        => 'integer',
			'body_font'            => $this->fonts,
			'body_size'            => 'integer',
			'body_color'           => 'color',
			'body_link_color'      => 'color',
			'body_orientation'     => array( 'left', 'center', 'right' ),
			'dropcap_font'         => $this->fonts,
			'initial_dropcap'      => 'boolean', // boolean is internally a 'yes' or 'no' string
			'dropcap_color'        => 'color',
			'byline_font'          => $this->fonts,
			'byline_size'          => 'integer',
			'byline_color'         => 'color',
			'header_font'          => $this->fonts,
			'header_color'         => 'color',
			'header1_size'         => 'integer',
			'header2_size'         => 'integer',
			'header3_size'         => 'integer',
			'header4_size'         => 'integer',
			'header5_size'         => 'integer',
			'header6_size'         => 'integer',
			'pullquote_font'       => $this->fonts,
			'pullquote_size'       => 'integer',
			'pullquote_color'      => 'color',
			'pullquote_transform'  => array( 'none', 'uppercase' ),
			'gallery_type'         => array( 'gallery', 'mosaic' ),
			'enable_advertisement' => array( 'yes', 'no' ),
		);

		$this->field_labels = array(
			'api_key'      => 'API key',
			'api_secret'   => 'API secret',
			'api_channel'  => 'API channel',
			'api_autosync' => 'Automatically publish to Apple News',
		);

		$this->loaded_settings = null;

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
			// Use select2 only when there is a considerable ammount of options available
			if ( count( $type ) > 10 ) {
				$field = '<select class="select2" name="%s">';
			} else {
				$field = '<select name="%s">';
			}
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
		} else if ( 'integer' == $type ) {
			$field = '<input required type="number" name="%s" value="%s">';
		} else if ( 'color' == $type ) {
			$field = '<input required type="color" name="%s" value="%s">';
		} else if ( 'password' == $type ) {
			$field = '<input required type="password" name="%s" value="%s">';
		} else {
			// If nothing else matches, it's a string.
			$field = '<input required type="text" name="%s" value="%s">';
		}

		printf( $field, $name, $value );
	}

	public function print_general_settings_section_info() {
		echo 'Settings which apply globally to the plugin functionality.';
	}

	private function get_setting_title( $name ) {
		if ( isset( $this->field_labels[ $name ] ) ) {
			return $this->field_labels[ $name ];
		}

		return ucfirst( str_replace( '_', ' ', $name ) );
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
				$this->get_setting_title( $name ),         // Title
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
		$this->register_assets();

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

	private function register_assets() {
		wp_enqueue_style( 'apple-export-select2-css', plugin_dir_url( __FILE__ ) .
			'../vendor/select2/select2.min.css', array() );

		wp_enqueue_script( 'apple-export-select2-js', plugin_dir_url( __FILE__ ) .
			'../vendor/select2/select2.full.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'apple-export-settings-js', plugin_dir_url( __FILE__ ) .
			'../assets/js/settings.js', array( 'jquery', 'apple-export-select2-js' )
		);
	}

	private function get_type_for_field( $name ) {
		if ( array_key_exists( $name, $this->field_types ) ) {
			return $this->field_types[ $name ];
		}

		return 'string';
	}

	/**
	 * Creates a new Settings instance and loads it with WordPress' saved
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
