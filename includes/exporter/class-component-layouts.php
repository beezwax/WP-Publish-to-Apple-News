<?php
namespace Exporter;

use \Exporter\Components\Component as Component;
use \Exporter\Components\Body as Body;

/**
 * Exporter and components can register layouts. This class manages the layouts
 * the final JSON will contain.
 *
 * @since 0.4.0
 */
class Component_Layouts {

	private $layouts;
	private $settings;

	function __construct( $settings ) {
		$this->layouts  = array();
		$this->settings = $settings;
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
	public function to_array() {
		return $this->layouts;
	}

	private function get_setting( $name ) {
		return $this->settings->get( $name );
	}

	private function layout_exists( $name ) {
		return array_key_exists( $name, $this->layouts );
	}

	public function set_anchor_layout_for( $component ) {
		// TODO: What do? Show centered? Ignore anchoring for now
		if ( 'center' == $this->get_setting( 'body_orientation' ) ) {
			return;
		}

		if ( ! $this->layout_exists( 'anchor_layout' ) ) {
			// Find out the starting column
			$col_start = 0;
			switch ( $this->get_setting( 'body_orientation' ) ) {
			case 'left':
				$col_start = Body::COLUMN_SPAN - Component::ALIGNMENT_OFFSET;
				break;
			case 'right':
				$col_start = 0;
				break;
			}

			$this->register_layout( 'anchor-layout', array(
				'columnStart' => $col_start,
				'columnSpan'  => Exporter::LAYOUT_COLUMNS - Body::COLUMN_SPAN + Component::ALIGNMENT_OFFSET,
			) );
		}

		$component->set_json( 'layout', 'anchor-layout' );
		// TODO: Use an animation manager
		$component->set_json( 'animation', array(
			'type'             => 'fade_in',
			'userControllable' => 'true',
			'initialAlpha'     => 0.0,
		) );
	}

}
