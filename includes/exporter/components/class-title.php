<?php
namespace Exporter\Components;

class Title extends Component {

	protected function build( $text ) {
		$this->json = array(
			'role' => 'title',
			'text' => $text,
		);
	}

}

