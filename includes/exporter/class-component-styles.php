<?php
namespace Exporter;

/**
 * Exporter and components can register styles. This class manages the styles
 * the final JSON will contain.
 *
 * @since 0.4.0
 */
class Component_Styles {

	private $styles;

	function __construct() {
		$this->styles = array();
	}

	public function register_style( $name, $spec ) {
		// Only register once, styles have unique names.
		if( array_key_exists( $name, $this->styles ) ) {
			return;
		}

		$this->styles[ $name ] = $spec;
	}

	public function get_styles() {
		return $this->styles;
	}

}
