<?php
namespace Exporter\Components;

/**
 * An HTML's divider (hr) representation.
 *
 * @since 0.2.0
 */
class Divider extends Component {

	public static function node_matches( $node ) {
		if ( 'hr' == $node->nodeName ) {
			return $node;
		}

		return null;
	}

	protected function build( $text ) {
		$this->json = array(
			'role'   => 'divider',
			'layout' => 'divider-layout',
			'stroke' => array( 'color' => '#E6E6E6', 'width' => 1 ),
		);

		$this->register_full_width_layout( 'divider-layout', array(
			'margin' => array( 'top' => 25, 'bottom' => 25 )
		) );
	}

}

