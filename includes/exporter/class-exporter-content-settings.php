<?php
namespace Exporter;

/**
 * Settings used for exporting a single "Exporter_Content" element.
 */
class Exporter_Content_Settings {

	// Exporter's default settings.
	private $settings = array(
		'pullquote'          => '',
		'pullquote_position' => 'top',
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
