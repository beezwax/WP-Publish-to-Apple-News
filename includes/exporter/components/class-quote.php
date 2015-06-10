<?php
namespace Exporter\Components;

/**
 * An HTML's blockquote representation.
 *
 * @since 0.0.0
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

