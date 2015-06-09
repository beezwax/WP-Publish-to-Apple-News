<?php
namespace Exporter\Components;

/**
 * An image gallery is basically a container with 'gallery' class and some
 * images inside.
 */
class Quote extends Component {

	protected function build( $text ) {
		// Remove initial and trailing tags: <blockquote><p>...</p></blockquote>
		$text = substr( $text, 15, -17 );

		$this->json = array(
			'role' => 'quote',
			'text' => $text,
		);
	}

}

