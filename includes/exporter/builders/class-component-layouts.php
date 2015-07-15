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

	/**
	 * Sets the required layout for a component to anchor another component or
	 * be anchored.
	 */
	public function set_anchor_layout_for( $component ) {
		// Are we anchoring left or right?
		$position = null;
		switch ( $component->anchor_position ) {
		case Component::ANCHOR_NONE:
			return;
		case Component::ANCHOR_LEFT:
			$position = 'left';
			break;
		case Component::ANCHOR_RIGHT:
			$position = 'right';
			break;
		case Component::ANCHOR_AUTO:
			// The alignment position is the opposite of the body_orientation
			// setting. In the case of centered body orientation, use left alignment.
			// This behaviour was chosen by design.
			if ( 'left' == $this->get_setting( 'body_orientation' ) ) {
				$position = 'right';
			} else {
				$position = 'left';
			}
			break;
		}

		$layout_name = "anchor-layout-$position";

		if ( ! $this->layout_exists( $layout_name ) ) {
			// Find out the starting column. This is easy enough if we are anchoring
			// left, but for right side alignment, we have to make some math :)
			$col_start = 0;
			if ( 'right' == $position ) {
				$col_start = Body::COLUMN_SPAN - Component::ALIGNMENT_OFFSET;

				if ( $component->is_anchor_target() ) {
					$col_start += 1;
				}
			}

			// Find the column span. For the target element, let's use the same
			// column span as the Body component, that is, 5 columns, minus the
			// defined offset. The element to be anchored uses the remaining space.
			$col_span = 0;
			if ( $component->is_anchor_target() ) {
				$col_span = Body::COLUMN_SPAN - Component::ALIGNMENT_OFFSET;
			} else {
				$col_span = Exporter::LAYOUT_COLUMNS - Body::COLUMN_SPAN + Component::ALIGNMENT_OFFSET;
			}

			// Finally, register the layout
			$this->register_layout( $layout_name, array(
				'columnStart' => $col_start,
				'columnSpan'  => $col_span,
			) );
		}

		$component->set_json( 'layout', $layout_name );
		// TODO: Use an animation manager
		$component->set_json( 'animation', array(
			'type'             => 'fade_in',
			'userControllable' => 'true',
			'initialAlpha'     => 0.0,
		) );
	}

}
