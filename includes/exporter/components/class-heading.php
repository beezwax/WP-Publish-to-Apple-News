<?php
namespace Exporter\Components;

class Heading extends Component {

	protected function build( $text ) {
		if ( 0 === preg_match( '/<h(\d)>(.*?)<\/h\1>/im', $text, $matches ) ) {
			return;
		}

		$this->json = array(
			'role' => 'heading' . $matches[1],
			'text' => $matches[2],
			'textStyle' => 'title',
		);
	}

}

