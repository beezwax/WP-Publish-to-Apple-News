<?php

/**
 * Describes a WordPress setting section
 *
 * @since 0.6.0
 */
class Admin_Settings_Section extends Apple_Export {

	/**
	 * All available iOS fonts.
	 *
	 * @since 0.4.0
	 */
	protected static $fonts = array(
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

	protected $name;
	protected $slug;
	protected $page;
	protected $base_settings;
	protected $settings = array();
	protected $groups   = array();

	function __construct( $page ) {
		$this->page          = $page;
		$base_settings       = new \Exporter\Settings;
		$this->base_settings = $base_settings->all();
	}

	public function name() {
		return $this->name;
	}

	/**
	 * Return an array which contains all groups and their related settings,
	 * embedded.
	 */
	public function groups() {
		$result = array();
		foreach( $this->groups as $name => $info ) {
			$settings = array();
			foreach( $info['settings'] as $name ) {
				$settings[ $name ] = $this->settings[ $name ];
				$settings[ $name ]['default'] = $this->get_default_for( $name );
			}

			$result[ $name ] = array(
				'label'       => $info['label'],
				'description' => @$info['description'] ?: null,
				'settings'    => $settings,
			);
		}

		return $result;
	}

	public function id() {
		return $this->plugin_slug . '_options_section_' . $this->slug;
	}

	public function register() {
		add_settings_section(
			$this->id(),
			$this->name,
			array( $this, 'print_section_info' ),
			$this->page
	 	);

		foreach ( $this->settings as $name => $options ) {
			register_setting( $this->page, $name );
			add_settings_field(
				$name,                                          // ID
				$options['label'],                              // Title
				array( $this, 'render_field' ),                 // Render calback
				$this->page,                                    // Page
				$this->id(),                                    // Section
				array( $name, $this->get_default_for( $name ) ) // Args passed to the render callback
		 	);
		}
	}

	public function render_field( $args ) {
		list( $name, $default_value ) = $args;
		$type  = $this->get_type_for( $name );
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
		} else if ( 'font' == $type ) {
			$field = '<select class="select2" name="%s">';
			foreach ( self::$fonts as $option ) {
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

	private function get_type_for( $name ) {
		return @$this->settings[ $name ]['type'] ?: 'string';
	}

	private function get_default_for( $name ) {
		return $this->base_settings[ $name ];
	}

	public function print_section_info() {
		return;
	}

}
