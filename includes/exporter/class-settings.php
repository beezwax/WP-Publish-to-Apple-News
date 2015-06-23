<?php
namespace Exporter;

/**
 * Settings used in exporting. In a WordPress context, these can be loaded
 * as WordPress options defined in the plugin.
 */
class Settings {

	// Default settings.
	private $settings = array(
		'layout_width'    => '1024',
		'layout_columns'  => '7', // Because we start counting at 0, this is actually 7, but show it as 8.
		'layout_margin'   => '30',
		'layout_gutter'   => '20',

		// This is a string containing the grid definition for the exporter.
		// For exampe, "4 4" will make two equal columns, "2 4 2" will make three
		// columns.
		// Note that the sum of all numbers must be equal to "layout_columns",
		// otherwise, it will be ignored.
		//'grid'            => '4 3',
		'grid'            => '7',

		'body_font'       => 'AvenirNext-Regular',
		'body_size'       => 16,
		'body_color'      => '#000',
		'body_link_color' => '#428BCA',
		'initial_dropcap' => 'yes',
		'dropcap_font'    => 'Georgia-Bold',
		'dropcap_color'   => '#000',

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

		'gallery_type'   => 'gallery', // this can either be gallery or mosaic.
	);

	public function get( $name ) {
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

}
