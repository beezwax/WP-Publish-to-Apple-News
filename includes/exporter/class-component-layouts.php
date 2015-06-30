<?php
namespace Exporter;

use \Exporter\Components\Component as Component;

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
	}

	/**
	 * Register a layout into the exporter.
	 *
	 * @since 0.4.0
	 */
	public function register_layout( $name, $spec ) {
		// Only register once, layouts have unique names.
		if ( $this->layout_exists( $name ) ) {
			return;
		}

		$this->layouts[ $name ] = $spec;
	}

	/**
	 * Returns all layouts registered so far.
	 *
	 * @since 0.4.0
	 */
	public function get_layouts() {
		return $this->layouts;
	}

	private function layout_exists( $name ) {
		return array_key_exists( $name, $this->layouts );
	}

	/**
	 * When a component is next to an aligned component (which is a component
	 * that must be displayed next to another one), the layout must be different,
	 * as it has less space. @see \Exporter\Components\Component::is_alignable.
	 *
	 * @since 0.4.0
	 */
	private function fix_layout_for_component( $component ) {
		// Create fix layout if not existant
		if ( ! $this->layout_exists( 'aligned-other' ) ) {
			// Get layout data
			$layout_name  = $component->get_json( 'layout' );
			$layout_value = $this->layouts[ $layout_name ];

			// Register new layout using the appropriate start and span
			$col_start = 0 == $layout_value[ 'columnStart' ] ?: $layout_value[ 'columnStart' ] + Component::ALIGNMENT_OFFSET;
			$col_span  = $component::COLUMN_SPAN - Component::ALIGNMENT_OFFSET;
			$this->register_layout( 'aligned-other', array(
				'columnStart' => $col_start,
				'columnSpan'  => $col_span,
			) );
		}

		// Use the aligned-other layout instead
		$component->set_json( 'layout', 'aligned-other' );
		return $component;
	}

	public function fix_alignments( $components ) {
		$must_fix = false;
		$result   = array();

		foreach ( $components as $component ) {
			if ( $must_fix ) {
				$must_fix  = false;
				$component = $this->fix_layout_for_component( $component );
			} else if ( $component->is_alignable ) {
				$must_fix = true;
			}

			$result[] = $component;
		}

		return $result;
	}

}
