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
