<?php
namespace Exporter\Components;

/**
 * Some Exporter_Content object might have an intro parameter.
 * This component does not need a node so no need to implement match_node.
 *
 * @since 0.0.0
 */
class Intro extends Component {

	protected function build( $text ) {
		$this->json = array(
			'role' => 'intro',
			'text' => $text,
		);
	}

}

