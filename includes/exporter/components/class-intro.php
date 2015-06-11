<?php
namespace Exporter\Components;

/**
 * Some Exporter_Content object might have an intro parameter. This is not read
 * from HTML so no need to parse the text.
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

