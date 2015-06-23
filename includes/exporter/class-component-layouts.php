<?php
namespace Exporter;

/**
 * Exporter and components can register layouts. This class manages the layouts
 * the final JSON will contain.
 *
 * @since 0.4.0
 */
class Component_Layouts {

	private $layouts;

	function __construct() {
		$this->layouts  = array();

		// Register default styles. full-width is used by components to always use
		// all width. When not in the first column, components shrink, so the grid
		// needs to force them to use all available space.
		$this->register_layout( 'full-width', array( 'columnStart' => 0 ) );
	}

	public function register_layout( $name, $spec ) {
		// Only register once, layouts have unique names.
		if ( array_key_exists( $name, $this->layouts ) ) {
			return;
		}

		$this->layouts[ $name ] = $spec;
	}

	/**
	 * Ask for layouts for a given ammount of components.
	 */
	public function get_layouts() {
		return $this->layouts;
	}

}
