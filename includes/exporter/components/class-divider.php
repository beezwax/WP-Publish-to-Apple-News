<?php
namespace Exporter\Components;

class Divider extends Component {

	protected function build( $text ) {
		$this->json = array(
			'role' => 'divider',
		);
	}

}

