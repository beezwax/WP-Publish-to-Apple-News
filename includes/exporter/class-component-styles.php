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

	/**
	 * Register a style into the exporter.
	 *
	 * @since 0.4.0
	 */
	public function register_style( $name, $spec ) {
		// Only register once, styles have unique names.
		if ( array_key_exists( $name, $this->styles ) ) {
			return;
		}

		$this->styles[ $name ] = $spec;
	}

	/**
	 * Returns all styles defined so far.
	 *
	 * @since 0.4.0
	 */
	public function get_styles() {
		return $this->styles;
	}

}
