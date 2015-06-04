<?php
namespace Exporter\Components;

class Body extends Component {

	protected function build( $text ) {
		// Remove initial and trailing tags
		$text = substr( $text, 3, -4 );

		$this->json = array(
			'role' => 'body',
			'text' => $text,
		);
	}

}

