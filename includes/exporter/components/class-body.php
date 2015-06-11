<?php
namespace Exporter\Components;

/**
 * One of the simplest components. It's just a paragraph.
 *
 * @since 0.0.0
 */
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

