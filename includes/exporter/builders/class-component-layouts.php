<?php
namespace Exporter\Builders;

use \Exporter\Components\Component as Component;
use \Exporter\Components\Body as Body;
use \Exporter\Exporter as Exporter;

/**
 * Exporter and components can register layouts. This class manages the layouts
 * the final JSON will contain.
 *
 * @since 0.4.0
 */
class Component_Layouts extends Builder {

	private $layouts;

	function __construct( $content, $settings ) {
		parent::__construct( $content, $settings );
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
	protected function build() {
		return $this->layouts;
	}

	private function layout_exists( $name ) {
		return array_key_exists( $name, $this->layouts );
	}

	public function set_anchor_target_layout_for( $component ) {
		// TODO: What do? Show centered? Ignore anchoring for now
		if ( 'center' == $this->get_setting( 'body_orientation' ) ) {
			return;
		}

		if ( ! $this->layout_exists( 'anchor-target-layout' ) ) {
			// Find out the starting column
			$col_span = 0;
			switch ( $this->get_setting( 'body_orientation' ) ) {
			case 'right':
				$col_span = Body::COLUMN_SPAN - Component::ALIGNMENT_OFFSET;
				break;
			case 'left':
				$col_span = 0;
				break;
			}

			$this->register_layout( 'anchor-target-layout', array(
				'columnStart' => Exporter::LAYOUT_COLUMNS - Body::COLUMN_SPAN + Component::ALIGNMENT_OFFSET,
				'columnSpan'  => $col_span,
			) );
		}

		$component->set_json( 'layout', 'anchor-target-layout' );
	}

	public function set_anchor_layout_for( $component ) {
		// TODO: What do? Show centered? Ignore anchoring for now
		if ( 'center' == $this->get_setting( 'body_orientation' ) ) {
			return;
		}

		if ( ! $this->layout_exists( 'anchor-layout' ) ) {
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
