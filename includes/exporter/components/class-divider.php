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
		);

		$this->register_layout( 'divider-layout', array(
			'margin' => array(
				'top'    => 30,
				'bottom' => 30,
			)
		) );
	}

}

