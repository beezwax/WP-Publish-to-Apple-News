<?php
namespace Exporter;

/**
 * Settings used in exporting. In a WordPress context, these can be loaded
 * as WordPress options defined in the plugin.
 */
class Settings {

	// Default settings.
	private $settings = array(
		'initial_dropcap' => true,
		'header_font'     => 'AvenirNext-Bold',
		'header_color'    => '#000',
		'header1_size'    => 48,
		'header2_size'    => 32,
		'header3_size'    => 24,
		'header4_size'    => 21,
		'header5_size'    => 18,
		'header6_size'    => 16,

		'body_font'       => 'AvenirNext-Regular',
		'body_size'       => 16,
		'body_color'      => '#000',
		'body_link_color' => '#428BCA',
		'dropcap_font'    => 'Georgia-Bold',
		'dropcap_color'   => '#000',

		'pullquote_font'  => 'DINCondensed-Bold',
		'pullquote_size'  => 48,
		'pullquote_color' => '#53585F',
		'pullquote_transform' => 'uppercase',

		'gallery_type'   => 'gallery', // this can either be gallery or mosaic.
	);

	public function get( $name ) {
		if( ! array_key_exists( $name, $this->settings ) ) {
			return null;
		}

		return $this->settings[ $name ];
	}

	public function set( $name, $value ) {
		$this->settings[ $name ] = $value;
	}

}
