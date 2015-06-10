<?php
namespace Exporter\Components;

/**
 * An HTML's divider (hr) representation.
 *
 * @since 0.0.0
 */
class Divider extends Component {

	protected function build( $text ) {
		$this->json = array(
			'role' => 'divider',
		);
	}

}

