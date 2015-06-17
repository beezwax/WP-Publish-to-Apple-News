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
		// Default styles
		$this->styles = array(
			'default' => array(
				'fontName' => 'Helvetica',
				'fontSize' => 13,
				'linkStyle' => array( 'textColor' => '#428bca' ),
			),
		);
	}

	public function register_style( $name, $spec ) {
		$this->styles[ $name ] = $spec;
	}

	public function get_styles() {
		return $this->styles;
	}

}
