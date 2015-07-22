<?php
namespace Exporter;

/**
 * Settings used in exporting. In a WordPress context, these can be loaded
 * as WordPress options defined in the plugin.
 */
class Settings {

	// Exporter's default settings.
	private $settings = array(
		// API information.
		'api_key'         => '',
		'api_secret'      => '',
		'api_channel'     => '',
		'api_autosync'    => 'yes',

		'layout_margin'   => '75',
		'layout_gutter'   => '20',

		'body_font'        => 'AvenirNext-Regular',
		'body_size'        => 18,
		'body_color'       => '#000',
		'body_link_color'  => '#428BCA',
		'body_orientation' => 'left',
		'body_line_height' => 1.35,

		'initial_dropcap' => 'yes',
		'dropcap_font'    => 'Georgia-Bold',
		'dropcap_color'   => '#000',

		'byline_font'     => 'AvenirNext-Medium',
		'byline_size'     => 16,
		'byline_color'    => '#53585F',

		'header_font'     => 'AvenirNext-Bold',
		'header_color'    => '#000',
		'header1_size'    => 48,
		'header2_size'    => 32,
		'header3_size'    => 24,
		'header4_size'    => 21,
		'header5_size'    => 18,
		'header6_size'    => 16,

		'pullquote_font'  => 'DINCondensed-Bold',
		'pullquote_size'  => 48,
		'pullquote_color' => '#53585F',
		'pullquote_transform' => 'uppercase',

		// This can either be gallery or mosaic.
		'gallery_type'   => 'gallery',

		'enable_advertisement' => 'yes',
	);

	public function get( $name ) {
		// Check for computed settings
		if ( method_exists( $this, $name ) ) {
			return $this->$name();
		}

		// Check for regular settings
		if ( ! array_key_exists( $name, $this->settings ) ) {
			return null;
		}

		return $this->settings[ $name ];
	}

	public function set( $name, $value ) {
		$this->settings[ $name ] = $value;
		return $value;
	}

	public function all() {
		return $this->settings;
	}

	// COMPUTED SETTINGS are those settings which are not shown in the frontend
	// and cannot be changed directly, instead, they are a logical representation
	// of a combination of other settings. For example, if the body orientation
	// is "center", the layout_width computed property is 768, otherwise, it's
	// 1024.
	// -------------------------------------------------------------------------

	public function layout_width() {
		return 'center' == $this->get( 'body_orientation' ) ? 768 : 1024;
	}

	public function layout_columns() {
		return 'center' == $this->get( 'body_orientation' ) ? 9 : 7;
	}

	public function body_column_span() {
		return 'center' == $this->get( 'body_orientation' ) ? 7 : 5;
	}

	/**
	 * When a component is displayed aligned relative to another one, slide the
	 * other component a few columns. This varies for centered and non-centered
	 * layouts, as centered layouts have more columns.
	 *
	 * @since 0.4.0
	 */
	public function alignment_offset() {
		return 'center' == $this->get( 'body_orientation' ) ? 3 : 2;
	}

}
